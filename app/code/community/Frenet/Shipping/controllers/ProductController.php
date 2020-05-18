<?php

class Frenet_Shipping_ProductController extends Frenet_Shipping_Controller_Front_Action
{
    use Frenet_Shipping_Helper_ObjectsTrait;

    /**
     * @var Frenet_Shipping_Model_Catalog_Product_View_QuoteInterface
     */
    private $quoteProduct;

    protected function _construct()
    {
        $this->quoteProduct = $this->objects()->productViewQuote();
    }

    public function quoteAction()
    {
        $productId = (int)    $this->getRequest()->getPost('product');
        $postcode  = (string) $this->getRequest()->getPost('postcode');
        $qty       = (float)  $this->getRequest()->getPost('qty');
        $options   = (array)  $this->getRequest()->getPost();

        $this->getResponse()->setHeader('Content-type', 'application/json', true);

        try {
            $rates = (array) $this->quoteProduct->quoteByProductId($productId, $postcode, $qty, $options);
            $this->getResponse()->setBody($this->prepareResponseSuccess($rates));
        } catch (\Exception $exception) {
            $this->getResponse()->setBody($this->prepareResponseError($exception));
        }
    }

    /**
     * @param array $data
     *
     * @return string
     */
    private function prepareResponseSuccess(array $data)
    {
        return $this->encodeResponse(
            [
                'error' => false,
                'rates' => $data
            ]
        );
    }

    /**
     * @param Exception $exception
     *
     * @return string
     */
    private function prepareResponseError(Exception $exception)
    {
        return $this->encodeResponse(
            [
                'error' => false,
                'message' => $exception->getMessage()
            ]
        );
    }
}
