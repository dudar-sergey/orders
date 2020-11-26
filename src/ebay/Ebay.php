<?php


namespace App\ebay;



use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class Ebay
{
    private $requestToken;
    //private $devID;
    //private $appID;
    //private $certID;
    private $serverUrl;
    private $compatLevel;
    private $siteID;
    private $verb;
    private $client;

    /**    __construct
     * Constructor to make a new instance of eBaySession with the details needed to make a call
     * Input:    $userRequestToken - the authentication token fir the user making the call
     * $developerID - Developer key obtained when registered at http://developer.ebay.com
     * $applicationID - Application key obtained when registered at http://developer.ebay.com
     * $certificateID - Certificate key obtained when registered at http://developer.ebay.com
     * $useTestServer - Boolean, if true then Sandbox server is used, otherwise production server is used
     * $compatabilityLevel - API version this is compatable with
     * $siteToUseID - the Id of the eBay site to associate the call iwht (0 = US, 2 = Canada, 3 = UK, ...)
     * $callName  - The name of the call being made (e.g. 'GeteBayOfficialTime')
     * Output:    Response string returned by the server
     */
    public function __construct()
    {
        $this->serverUrl = 'https://api.sandbox.ebay.com/ws/api.dll';
        $this->requestToken = 'AgAAAA**AQAAAA**aAAAAA**GDZ0Xw**nY+sHZ2PrBmdj6wVnY+sEZ2PrA2dj6wFk4aiDJCFpASdj6x9nY+seQ**BWAFAA**AAMAAA**RCeuqehuTBrW9E6n2CSKWaCK/3OjGuwv9vKKcdLnUNSGkPjiuG1BIQpalYMgSQwD5Oig98kmLdj0UyHAbdTMFTkWKYBEHJv6ncMor5V0Yr4wAV50jwRc5ih05PTzAGrka9PI+HNOjmjKSj2UaYx9gT+6R27FvXL36rQeL1IzIGw7BdPX28BZhjd2ujEqC2bKLE8QAc9EK3PYwgMyoXnEzdh1hqHA7brgkYunxqbI2Vqd9x5iPXCiDEbpFTIHMPRz+mOoyecH+XOApkLWzfWPhsDXOapyRvkKkLBaVcOaHx0kYhzk3VQtNmPyi//WdxgaWcs3bZeHQxlXKCJKNBoPkxn8kFANSTzQJpWJUvWNkPcLI3fM5D4MGhokjkXJmgdmmaSAn5IjKbuHdw4/sFGNAVItstlQs/G9xlzuxYwXgNBnsmOdynTCbS0BXr7PlZzqbAdQMgH14n4l3CKF5tqSfxWDaw2KWRZF7gBNLZKb1u5XtH/K84xoTn2rmHU7WA0RKT6Q+Y5DD5KZvSqBzKaLSwSKzykNI0WONlmiHAgiqqjjhx80yyG9DER7NjRhWmgOgKsfTDawwXQX2N+c0j5ZbWAGD5flwRMOpE9N6t0vMPNldr8RmBMDSkZatjkpIMLOQKlAIbzzJ9VVXhxRBVBquNn1q9TkQiOhGZhDlYEm3BOJmMZl4dSbDEt04/Z+qU3VT0EJx5MePtSxb2CFV0Beboi4c+0rFnCEdRGAZlcrtXmXbexHuuUAxom4vQ3DfnNQ';
        $this->siteID = 0;
        $this->client = HttpClient::create([
            //'proxy'=>'http://5wS6f0:g81SSX@185.147.130.52:8000',
        ]);

    }

    public function getOrdersFromEbay()
    {
        $con = null;
        $requestBody = $this->getXMLForOrders();
        $this->verb = 'GetOrders';
        $this->compatLevel = '967';

        $response = $this->client->request('POST', $this->serverUrl, [
            'headers' => $this->buildEbayHeaders(),
            'body' => $requestBody,
        ]);

        if($response)
        {
            $con = json_encode(simplexml_load_string($response->getContent()));
        }
        return json_decode($con, true);
    }

    public function getProductsFromEbay()
    {
        $con = null;
        $requestBody = $this->getXMLForProducts();
        $this->verb = 'GetMyeBaySelling';
        $this->compatLevel = '967';

        $response = $this->client->request('POST', $this->serverUrl, [
            'headers' => $this->buildEbayHeaders(),
            'body' => $requestBody,
        ]);

        if($response)
        {
            $con = json_encode(simplexml_load_string($response->getContent()));
        }
        return json_decode($con, true);
    }

    /**	buildEbayHeaders
    Generates an array of string to be used as the headers for the HTTP request to eBay
    Output:	String Array of Headers applicable for this call
     */
    private function buildEbayHeaders()
    {
        return array (
            //Regulates versioning of the XML interface for the API
            'X-EBAY-API-COMPATIBILITY-LEVEL' => $this->compatLevel,

            //set the keys
            //'X-EBAY-API-DEV-NAME: ' . $this->devID,
            //'X-EBAY-API-APP-NAME: ' . $this->appID,
            //'X-EBAY-API-CERT-NAME: ' . $this->certID,

            //the name of the call we are requesting
            'X-EBAY-API-CALL-NAME' => $this->verb,

            //SiteID must also be set in the Request's XML
            //SiteID = 0  (US) - UK = 3, Canada = 2, Australia = 15, ....
            //SiteID Indicates the eBay site to associate the call with
            'X-EBAY-API-SITEID' => $this->siteID,
            'Content-Type' => 'text/xml; charset=UTF8',
        );
    }

    private function getXMLForProducts()
    {
        $xmlBody = '<?xml version="1.0" encoding="utf-8"?>';
        $xmlBody .= '<GetMyeBaySellingRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
        $xmlBody .='<RequesterCredentials>';
        $xmlBody .=   '<eBayAuthToken>'.$this->requestToken.'</eBayAuthToken>';
        $xmlBody .=    '</RequesterCredentials>';
        $xmlBody .=    '<ErrorLanguage>en_US</ErrorLanguage>';
        $xmlBody .=    '<WarningLevel>High</WarningLevel>';
        $xmlBody .=  '<ActiveList>';
        $xmlBody .=  '<Sort>TimeLeft</Sort>';
        $xmlBody .=  '<Pagination>';
        $xmlBody .=   '<EntriesPerPage>10</EntriesPerPage>';
        $xmlBody .=   '<PageNumber>1</PageNumber>';
        $xmlBody .=   '</Pagination>';
        $xmlBody .=  '</ActiveList>';
        $xmlBody .= '</GetMyeBaySellingRequest>';

        return $xmlBody;
    }

    public function getXMLForOrders()
    {
        $xmlBody = '<?xml version="1.0" encoding="utf-8"?>';
        $xmlBody .= '<GetOrdersRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
        $xmlBody .= '<RequesterCredentials>';
        $xmlBody .=    '<eBayAuthToken>'.$this->requestToken.'</eBayAuthToken>';
        $xmlBody .=  '</RequesterCredentials>';
        $xmlBody .=    '<ErrorLanguage>en_US</ErrorLanguage>';
        $xmlBody .=   '<WarningLevel>High</WarningLevel>';
        $xmlBody .=  '<CreateTimeFrom>2015-12-01T20:34:44.000Z</CreateTimeFrom>';
        $xmlBody .=   '<CreateTimeTo>2021-12-01T20:34:44.000Z</CreateTimeTo>';
        $xmlBody .=  '<OrderRole>Seller</OrderRole>';
        $xmlBody .=  '<OrderStatus>Active</OrderStatus>';
        $xmlBody .= '</GetOrdersRequest>';
        return $xmlBody;
    }

    public function addItem($data)
    {
        $this->verb = 'AddFixedPriceItem';
        $this->compatLevel = 967;

        $xmlBody = '<?xml version="1.0" encoding="utf-8"?>
            <AddFixedPriceItemRequest xmlns="urn:ebay:apis:eBLBaseComponents">
              <RequesterCredentials>
                <eBayAuthToken>'.$this->requestToken.'</eBayAuthToken>
              </RequesterCredentials>
                <ErrorLanguage>en_US</ErrorLanguage>
                <WarningLevel>High</WarningLevel>
                <Item>
                    <Title>'.$data['title'].'</Title>
                    <Description>'.$data['description'].'</Description>
                  <PrimaryCategory>
                  <CategoryID>111422</CategoryID>
                 </PrimaryCategory>
                    <StartPrice>'.$data['price'].'</StartPrice>
                    <CategoryMappingAllowed>true</CategoryMappingAllowed>
                    <ConditionID>1000</ConditionID>
                    <Country>US</Country>
                    <Currency>USD</Currency>
                    <DispatchTimeMax>3</DispatchTimeMax>
                    <ListingDuration>Days_7</ListingDuration>
                    <ListingType>FixedPriceItem</ListingType>
                    <PaymentMethods>PayPal</PaymentMethods>
                      <!--Enter your Paypal email address-->
                    <PayPalEmailAddress>dudar.sr@yandex.ru</PayPalEmailAddress>
                    <PictureDetails>
                        <GalleryType>Gallery</GalleryType>
                    </PictureDetails>
                    <PostalCode>95125</PostalCode>
                    <ProductListingDetails>
                        <UPC>'.$data['upc'].'</UPC>
                        <IncludeStockPhotoURL>true</IncludeStockPhotoURL>
                        <IncludeeBayProductDetails>true</IncludeeBayProductDetails>
                        <UseFirstProduct>true</UseFirstProduct>
                        <UseStockPhotoURLAsGallery>true</UseStockPhotoURLAsGallery>
                        <ReturnSearchResultOnDuplicates>true</ReturnSearchResultOnDuplicates>
                    </ProductListingDetails>
                    <Quantity>'.$data['quantity'].'</Quantity>
                    <ReturnPolicy>
                        <ReturnsAcceptedOption>ReturnsAccepted</ReturnsAcceptedOption>
                        <RefundOption>MoneyBack</RefundOption>
                        <ReturnsWithinOption>Days_30</ReturnsWithinOption>
                        <Description>If you arenot satisfied, return the item for refund.</Description>
                        <ShippingCostPaidByOption>Buyer</ShippingCostPaidByOption>
                    </ReturnPolicy>
                    <ShippingDetails>
                        <ShippingType>Flat</ShippingType>
                        <ShippingServiceOptions>
                            <ShippingServicePriority>1</ShippingServicePriority>
                            <ShippingService>UPSGround</ShippingService>
                            <FreeShipping>true</FreeShipping>
                            <ShippingServiceAdditionalCost currencyID="USD">0.00</ShippingServiceAdditionalCost>
                        </ShippingServiceOptions>
                    </ShippingDetails>
                    <Site>US</Site>
                </Item>
            </AddFixedPriceItemRequest>';

        $response = $this->client->request('POST', $this->serverUrl, [
            'headers' => $this->buildEbayHeaders(),
            'body' => $xmlBody,
        ]);
        $con = json_encode(simplexml_load_string($response->getContent()));
        return json_decode($con, true);
    }

    public function getItem($id)
    {

    }
}