<?php
/**
 * @license Copyright 2011-2014 BitPay Inc., MIT License
 * see https://github.com/bitpay/php-bitpay-client/blob/master/LICENSE
 */

namespace Bitpay\Resource;

use Bitpay\Resource\Token;
use Bitpay\Resource\Invoice;
use Bitpay\Resource\Payout;
use Bitpay\Crypto\PublicKey;
use Bitpay\Crypto\PrivateKey;
use Bitpay\Network\Network;
use Bitpay\Network\Adapter;
use Bitpay\Network\Request;
use Bitpay\Network\Response;
use Bitpay\Network\CurlAdapter;

/**
 * Client used to send requests and receive responses for BitPay's Web API
 *
 * @package Bitpay
 */
class Client extends Resource
{
    /**
     * @var Request
     */
    private $request;

    /**
     * @var Response
     */
    private $response;

    /**
     * @var AdapterInterface
     */
    private $adapter;

    /**
     * @var PublicKey
     */
    private $publicKey;

    /**
     * @var PrivateKey
     */
    private $privateKey;

    /**
     * @var NetworkInterface
     */
    private $network;

    /**
     * The network is either livenet or testnet and tells the client where to
     * send the requests.
     *
     * @param NetworkInterface
     */
    public function setNetwork(Network $network)
    {
        $this->network = $network;
    }

    /**
     * Set the Public Key to use to help identify who you are to BitPay. Please
     * note that you must first pair your keys and get a token in return to use.
     *
     * @param PublicKey $key
     */
    public function setPublicKey(PublicKey $key)
    {
        $this->publicKey = $key;
    }

    /**
     * Set the Private Key to use, this is used when signing request strings
     *
     * @param PrivateKey $key
     */
    public function setPrivateKey(PrivateKey $key)
    {
        $this->privateKey = $key;
    }

    /**
     * @param Adapter $adapter
     */
    public function setAdapter(Adapter $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * @inheritdoc
     */
    public function createInvoice(Invoice $invoice)
    {
        $request = $this->createNewRequest();
        $request->setMethod(Request::METHOD_POST);
        $request->setPath('invoices');

        $currency     = $invoice->getCurrency();
        $item         = $invoice->getItem();
        $buyer        = $invoice->getBuyer();
        $buyerAddress = $buyer->getAddress();

        $body = array(
            'price'             => $item->getPrice(),
            'currency'          => $currency->getCode(),
            'posData'           => $invoice->getPosData(),
            'notificationURL'   => $invoice->getNotificationUrl(),
            'transactionSpeed'  => $invoice->getTransactionSpeed(),
            'fullNotifications' => $invoice->isFullNotifications(),
            'notificationEmail' => $invoice->getNotificationEmail(),
            'redirectURL'       => $invoice->getRedirectUrl(),
            'orderID'           => $invoice->getOrderId(),
            'itemDesc'          => $item->getDescription(),
            'itemCode'          => $item->getCode(),
            'physical'          => $item->isPhysical(),
            'buyerName'         => trim(sprintf('%s %s', $buyer->getFirstName(), $buyer->getLastName())),
            'buyerAddress1'     => isset($buyerAddress[0]) ? $buyerAddress[0] : '',
            'buyerAddress2'     => isset($buyerAddress[1]) ? $buyerAddress[1] : '',
            'buyerCity'         => $buyer->getCity(),
            'buyerState'        => $buyer->getState(),
            'buyerZip'          => $buyer->getZip(),
            'buyerCountry'      => $buyer->getCountry(),
            'buyerEmail'        => $buyer->getEmail(),
            'buyerPhone'        => $buyer->getPhone(),
            'guid'              => Network::guid(),
            'nonce'             => Network::nonce(),
            'token'             => $this->token->getToken(),
        );

        $request->setBody(json_encode($body));

        $this->addIdentityHeader($request);
        $this->addSignatureHeader($request);

        $this->request  = $request;
        $this->response = $this->sendRequest($request);

        $body = json_decode($this->response->getBody(), true);

        if (isset($body['error']) || isset($body['errors'])) {
            throw new \Exception('[ERROR] In Client::createInvoice: The response to our invoice creation request contained errors. The actual response returned was: ' . var_export($this->response, true));
        }

        $data = $body['data'];

        $invoice
            ->setId($data['id'])
            ->setUrl($data['url'])
            ->setStatus($data['status'])
            ->setBtcPrice($data['btcPrice'])
            ->setPrice($data['price'])
            ->setInvoiceTime($data['invoiceTime'])
            ->setExpirationTime($data['expirationTime'])
            ->setCurrentTime($data['currentTime'])
            ->setBtcPaid($data['btcPaid'])
            ->setRate($data['rate'])
            ->setExceptionStatus($data['exceptionStatus']);

        return $invoice;
    }

    /**
     * @inheritdoc
     */
    public function getCurrencies()
    {
        $this->request = $this->createNewRequest();
        $this->request->setMethod(Request::METHOD_GET);
        $this->request->setPath('currencies');

        $this->response = $this->sendRequest($this->request);
        $body           = json_decode($this->response->getBody(), true);

        if (true === empty($body['data'])) {
            throw new \Exception('[ERROR] In Client::getCurrencies: The data parameter in the response was empty. The actual response returned was: ' . var_export($this->response, true));
        }

        $currencies = $body['data'];

        $currency = new Currency();

        if (false === isset($currency) || true === empty($currency)) {
        	throw new \Exception('[ERROR] In Client::getCurrencies: Could not create new Currency object.');
        }

        array_walk($currencies, function (&$value, $key) {
            $currency
                ->setCode($value['code'])
                ->setSymbol($value['symbol'])
                ->setPrecision($value['precision'])
                ->setExchangePctFee($value['exchangePctFee'])
                ->setPayoutEnabled($value['payoutEnabled'])
                ->setName($value['name'])
                ->setPluralName($value['plural'])
                ->setAlts($value['alts'])
                ->setPayoutFields($value['payoutFields']);
            $value = $currency;
        });

        return $currencies;
    }

    /**
     * @inheritdoc
     */
    public function createPayout(Payout $payout)
    {
        $request = $this->createNewRequest();
        $request->setMethod($request::METHOD_POST);
        $request->setPath('payouts');

        $amount         = $payout->getAmount();
        $currency       = $payout->getCurrency();
        $effectiveDate  = $payout->getEffectiveDate();
        $token          = $payout->getToken();

        $body = array(
            'token'         => $token->getToken(),
            'amount'        => $amount,
            'currency'      => $currency->getCode(),
            'instructions'  => array(),
            'effectiveDate' => $effectiveDate,
            'pricingMethod' => $payout->getPricingMethod(),
            'guid'          => Network::guid(),
            'nonce'         => Network::nonce()
        );

        // Optional
        foreach (array('reference','notificationURL','notificationEmail') as $value) {
            $function = 'get' . ucfirst($value);
            if ($payout->$function() != null) {
                $body[$value] = $payout->$function();
            }
        }

        // Add instructions
        foreach ($payout->getInstructions() as $instruction) {
            $body['instructions'][] = array(
                'label'   => $instruction->getLabel(),
                'address' => $instruction->getAddress(),
                'amount'  => $instruction->getAmount()
            );
        }

        $request->setBody(json_encode($body));
        $this->addIdentityHeader($request);
        $this->addSignatureHeader($request);

        $this->request  = $request;
        $this->response = $this->sendRequest($request);

        $body = json_decode($this->response->getBody(), true);

        if (isset($body['error']) || isset($body['errors'])) {
            throw new \Exception('[ERROR] In Client::createPayout: The response to our payout request contained errors. The actual response returned was: ' . var_export($this->response, true));
        }

        $data = $body['data'];

        $payout
            ->setId($data['id'])
            ->setAccountId($data['account'])
            ->setResponseToken($data['token'])
            ->setStatus($data['status']);

        foreach ($data['instructions'] as $c => $instruction) {
            $payout->updateInstruction($c, 'setId', $instruction['id']);
        }

        return $payout;
    }

    /**
     * @inheritdoc
     */
    public function getPayouts($status = null)
    {
        $request = $this->createNewRequest();
        $request->setMethod(Request::METHOD_GET);

        $path = 'payouts?token=' . $this->token->getToken() . (($status == null) ? '' : '&status=' . $status);

        $request->setPath($path);

        $this->addIdentityHeader($request);
        $this->addSignatureHeader($request);

        $this->request  = $request;
        $this->response = $this->sendRequest($this->request);

        $body = json_decode($this->response->getBody(), true);

        if (isset($body['error']) || isset($body['errors'])) {
            throw new \Exception('[ERROR] In Client::getPayouts: The response to our payout request contained errors. The actual response returned was: ' . var_export($this->response, true));
        }

        $payouts = array();

        array_walk($body['data'], function ($value, $key) use (&$payouts) {
            $payout = new Payout();
            $payout
                ->setId($value['id'])
                ->setAccountId($value['account'])
                ->setCurrency(new Currency($value['currency']))
                ->setEffectiveDate($value['effectiveDate'])
                ->setRequestdate($value['requestDate'])
                ->setPricingMethod($value['pricingMethod'])
                ->setStatus($value['status'])
                ->setAmount($value['amount'])
                ->setResponseToken($value['token'])
                ->setRate(@$value['rate'])
                ->setBtcAmount(@$value['btc'])
                ->setReference(@$value['reference'])
                ->setNotificationURL(@$value['notificationURL'])
                ->setNotificationEmail(@$value['notificationEmail']);

            array_walk($value['instructions'], function ($value, $key) use (&$payout) {
                $instruction = new PayoutInstruction();
                $instruction
                    ->setId($value['id'])
                    ->setLabel($value['label'])
                    ->setAddress($value['address'])
                    ->setAmount($value['amount'])
                    ->setStatus($value['status']);

                array_walk($value['transactions'], function ($value, $key) use (&$instruction) {
                    $transaction = new PayoutTransaction();
                    $transaction
                        ->setTransactionId($value['txid'])
                        ->setAmount($value['amount'])
                        ->setDate($value['date']);

                    $instruction->addTransaction($transaction);
                });

                $payout->addInstruction($instruction);
            });

            $payouts[] = $payout;
        });

        return $payouts;
    }

    /**
     * @inheritdoc
     */
    public function deletePayout(PayoutInterface $payout)
    {
        $request = $this->createNewRequest();
        $request->setMethod(Request::METHOD_DELETE);
        $request->setPath(sprintf('payouts/%s?token=%s', $payout->getId(), $payout->getResponseToken()));

        $this->addIdentityHeader($request);
        $this->addSignatureHeader($request);

        $this->request  = $request;
        $this->response = $this->sendRequest($this->request);

        $body           = json_decode($this->response->getBody(), true);
        if (empty($body['data'])) {
            throw new \Exception('[ERROR] In Client::deletePayout: The response to our payout request contained errors. The actual response returned was: ' . var_export($this->response, true));
        }

        $data   = $body['data'];

        $payout->setStatus($data['status']);

        return $payout;
    }

    /**
     * @inheritdoc
     */
    public function getPayout($payoutId)
    {
        $request = $this->createNewRequest();
        $request->setMethod(Request::METHOD_GET);
        $request->setPath(sprintf('payouts/%s?token=%s', $payoutId, $this->token->getToken()));
        $this->addIdentityHeader($request);
        $this->addSignatureHeader($request);

        $this->request  = $request;
        $this->response = $this->sendRequest($this->request);

        $body           = json_decode($this->response->getBody(), true);
        if (empty($body['data'])) {
            throw new \Exception('[ERROR] In Client::getPayout: The response to our payout retrieval request contained errors. The actual response returned was: ' . var_export($this->response, true));
        }
        $data   = $body['data'];

        $payout = new Payout();
        $payout
            ->setId($data['id'])
            ->setAccountId($data['account'])
            ->setStatus($data['status'])
            ->setCurrency(new Currency($data['currency']))
            ->setRate(@$data['rate'])
            ->setAmount($data['amount'])
            ->setBtcAmount(@$data['btc'])
            ->setPricingMethod(@$data['pricingMethod'])
            ->setReference(@$data['reference'])
            ->setNotificationEmail(@$data['notificationEmail'])
            ->setNotificationUrl(@$data['notificationURL'])
            ->setRequestDate($data['requestDate'])
            ->setEffectiveDate($data['effectiveDate'])
            ->setResponseToken($data['token']);

        array_walk($data['instructions'], function ($value, $key) use (&$payout) {
            $instruction = new PayoutInstruction();
            $instruction
                ->setId($value['id'])
                ->setLabel($value['label'])
                ->setAddress($value['address'])
                ->setStatus($value['status'])
                ->setAmount($value['amount'])
                ->setBtc($value['btc']);

            array_walk($value['transactions'], function ($value, $key) use (&$instruction) {
                $transaction = new PayoutTransaction();
                $transaction
                    ->setTransactionId($value['txid'])
                    ->setAmount($value['amount'])
                    ->setDate($value['date']);

                $instruction->addTransaction($transaction);
            });

            $payout->addInstruction($instruction);
        });

        return $payout;
    }

    /**
     * @inheritdoc
     */
    public function getTokens()
    {
        $request = $this->createNewRequest();
        $request->setMethod(Request::METHOD_GET);
        $request->setPath('tokens');
        $this->addIdentityHeader($request);
        $this->addSignatureHeader($request);

        $this->request  = $request;
        $this->response = $this->sendRequest($this->request);

        $body = json_decode($this->response->getBody(), true);

        if (empty($body['data'])) {
            throw new \Exception('[ERROR] In Client::getTokens: The response to our token retrieval request contained errors. The actual response returned was: ' . var_export($this->response, true));
        }

        $tokens = array();

        array_walk($body['data'], function ($value, $key) use (&$tokens) {
            $key   = current(array_keys($value));
            $value = current(array_values($value));
            $token = new Token();
            $token
                ->setFacade($key)
                ->setToken($value);

            $tokens[$token->getFacade()] = $token;
        });

        return $tokens;
    }

    /**
     * @inheritdoc
     */
    public function createToken(array $payload = array())
    {
        if (isset($payload['pairingCode']) && 1 !== preg_match('/^[a-zA-Z0-9]{7}$/', $payload['pairingCode'])) {
            throw new \Exception('[ERROR] In Client::createToken: The pairing code value provided is invalid.');
        }

        $payload['guid'] = Network::guid();

        $this->request = $this->createNewRequest();
        $this->request->setMethod(Request::METHOD_POST);
        $this->request->setPath('tokens');
        $this->request->setBody(json_encode($payload));

        $this->response = $this->sendRequest($this->request);

        $body = json_decode($this->response->getBody(), true);

        if (isset($body['error'])) {
            throw new \Exception('[ERROR] In Client::createToken: The response to our token creation request contained errors. The actual response returned was: ' . var_export($this->response, true));
        }

        $tkn = $body['data'][0];
        $createdAt = new \DateTime();
        $pairingExpiration = new \DateTime();

        $token = new Token();

        $token
            ->setPolicies($tkn['policies'])
            ->setToken($tkn['token'])
            ->setFacade($tkn['facade'])
            ->setCreatedAt($createdAt->setTimestamp(floor($tkn['dateCreated']/1000)));

        if (isset($tkn['resource'])) {
            $token->setResource($tkn['resource']);
        }

        if (isset($tkn['pairingCode'])) {
            $token->setPairingCode($tkn['pairingCode']);
            $token->setPairingExpiration($pairingExpiration->setTimestamp(floor($tkn['pairingExpiration']/1000)));
        }

        return $token;
    }

    /**
     * Returns the Response object that BitPay returned from the request that
     * was sent
     *
     * @return ResponseInterface
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Returns the request object that was sent to BitPay
     *
     * @return RequestInterface
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @inheritdoc
     */
    public function getInvoice($invoiceId)
    {
        $this->request = $this->createNewRequest();
        $this->request->setMethod(Request::METHOD_GET);
        $this->request->setPath(sprintf('invoices/%s', $invoiceId));

        $this->response = $this->sendRequest($this->request);

        $body = json_decode($this->response->getBody(), true);

        if (isset($body['error'])) {
            throw new \Exception('[ERROR] In Client::getInvoice: The response to our invoice retrieval request contained errors. The actual response returned was: ' . var_export($this->response, true));
        }

        $data = $body['data'];

        $invoice = new Invoice();

        $invoice
            //->setToken($data['token'])
            //->setBtcDue($data['btcDue'])
            //->setExRates($data['exRates'])
            ->setUrl($data['url'])
            ->setPosData($data['posData'])
            ->setStatus($data['status'])
            ->setBtcPrice($data['btcPrice'])
            ->setPrice($data['price'])
            ->setCurrency(new Currency($data['currency']))
            ->setOrderId($data['orderId'])
            ->setInvoiceTime($data['invoiceTime'])
            ->setExpirationTime($data['expirationTime'])
            ->setCurrentTime($data['currentTime'])
            ->setId($data['id'])
            ->setBtcPaid($data['btcPrice'])
            ->setRate($data['rate'])
            ->setExceptionStatus($data['exceptionStatus']);

        return $invoice;
    }

    /**
     * @param Request
     * @return Response
     */
    public function sendRequest(Request $request)
    {
        if (null === $this->adapter) {
            // Uses the default adapter
            $this->adapter = new CurlAdapter();
        }

        return $this->adapter->sendRequest($request);
    }

    /**
     * @param  Request
     * @throws \Exception
     */
    public function addIdentityHeader(Request $request)
    {
        if (null === $this->publicKey) {
            throw new \Exception('Please set your Public Key.');
        }

        $request->setHeader('x-identity', (string) $this->publicKey);
    }

    /**
     * @param Request
     */
    public function addSignatureHeader(Request $request)
    {
        if (null === $this->privateKey) {
            throw new \Exception('Please set your Private Key');
        }

        if (true == property_exists($this->network, 'isPortRequiredInUrl')) {
            if ($this->network->isPortRequiredInUrl === true) {
                $url = $request->getUriWithPort();
            } else {
                $url = $request->getUri();
            }
        } else {
            $url = $request->getUri();
        }

        $message = sprintf(
            '%s%s',
            $url,
            $request->getBody()
        );

        $signature = $this->privateKey->sign($message);

        $request->setHeader('x-signature', $signature);
    }

    /**
     * Creates new request object and prepares some networking
     * parameters.
     *
     * @return Request
     */
    public function createNewRequest()
    {
        $request = new Request();

        $request->setHost($this->network->getApiHost());
        $request->setPort($this->network->getApiPort());
        $this->prepareRequestHeaders($request);

        return $request;
    }

    /**
     * Prepares the request object by adding additional headers
     *
     * @param Request
     */
    private function prepareRequestHeaders(Request $request)
    {
        // @see http://en.wikipedia.org/wiki/User_agent
        $request->setHeader(
            'User-Agent',
            sprintf('%s/%s (PHP %s)', self::NAME, self::VERSION, phpversion())
        );

        $request->setHeader('X-BitPay-Plugin-Info', sprintf('%s/%s', self::NAME, self::VERSION));
        $request->setHeader('Content-Type', 'application/json');
        $request->setHeader('X-Accept-Version', '2.0.0');
    }
}
