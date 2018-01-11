<?php

namespace Eccube\Tests\Service;

use Eccube\Application;
use Eccube\Entity\CartItem;
use Eccube\Entity\Master\ProductStatus;
use Eccube\Entity\Product;
use Eccube\Entity\ProductClass;
use Eccube\Service\PurchaseFlow\Processor\DisplayStatusValidator;
use Eccube\Service\PurchaseFlow\PurchaseContext;
use Eccube\Tests\EccubeTestCase;

class DisplayStatusValidatorTest extends EccubeTestCase
{
    /**
     * @var DisplayStatusValidator
     */
    protected $validator;

    /**
     * @var CartItem
     */
    protected $cartItem;

    /**
     * @var Product
     */
    protected $Product;

    /**
     * @var ProductClass
     */
    protected $ProductClass;

    public function setUp()
    {
        $this->markTestIncomplete(get_class($this).' は未実装です');
        parent::setUp();

        $this->Product = $this->createProduct('テスト商品', 1);
        $this->ProductClass = $this->Product->getProductClasses()[0];
        $this->validator = new DisplayStatusValidator();
        $this->cartItem = new CartItem();
        $this->cartItem->setQuantity(10);
        $this->cartItem->setProductClass($this->ProductClass);
    }

    public function testInstance()
    {
        self::assertInstanceOf(DisplayStatusValidator::class, $this->validator);
    }

    /**
     * 公開商品の場合はなにもしない.
     */
    public function testDisplayStatusWithShow()
    {
        $ProductStatus = $this->entityManager->find(ProductStatus::class, ProductStatus::DISPLAY_SHOW);
        $this->Product->setStatus($ProductStatus);

        $this->validator->process($this->cartItem, new PurchaseContext());

        self::assertEquals(10, $this->cartItem->getQuantity());
    }

    /**
     * 非公開商品の場合は明細の個数を0に設定する.
     */
    public function testDisplayStatusWithClosed()
    {
        $ProductStatus = $this->entityManager->find(ProductStatus::class, ProductStatus::DISPLAY_HIDE);
        $this->Product->setStatus($ProductStatus);

        $this->validator->process($this->cartItem, new PurchaseContext());

        self::assertEquals(0, $this->cartItem->getQuantity());
    }
}
