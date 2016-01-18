<?php foreach ($_productCollection as $_product): ?>
                         <?php $special = $_product->getSpecialPrice();
                              $actual = $_product->getPrice();
                        //if($special < $actual): ?>

                <div class="col-box" id="item<?php if (($i - 1) % $_columnCount == 0): ?> first<?php elseif ($i % $_columnCount == 0): ?> last<?php endif; ?>"><a href="<?php echo $_product->getProductUrl() ?>">
                        <?php
                        $special = $_product->getSpecialPrice();
                        $actual = $_product->getPrice();
                       
                        $percent = ($special / $actual) * 100;
                        $percentFinal = 100 - $percent;
                        ?>
                        <span class="discount"><?php echo number_format($percentFinal, '0', '.', ',') ?>%</span>
                        <div class="img-box"><div class="abs_center"><!-- <a href="<?php echo $_product->getProductUrl() ?>" title="<?php echo $this->stripTags($this->getImageLabel($_product, 'small_image'), null, true) ?>" class="product-image"> -->
        <?php $_imgSize = 210; ?>
                                <img id="product-collection-image-<?php echo $_product->getId(); ?>"
                                     src="<?php echo $this->helper('catalog/image')->init($_product, 'small_image')->resize($_imgSize); ?>"
                                     alt="<?php echo $this->stripTags($this->getImageLabel($_product, 'small_image'), null, true) ?>" />
                                <!-- </a> --></div></div>
                        <div class="bottom-area">
                            <h4><?php echo $_helper->productAttribute($_product, $_product->getName(), 'name') ?></h4>
                           <?php 
							  $specialPriceFromDate = Mage::getModel('catalog/product')->load($_product->getId())->getSpecialFromDate();
                              $specialPriceToDate = Mage::getModel('catalog/product')->load($_product->getId())->getSpecialToDate();
                             $today =  time();
							 
							?>
                           <?php   $specialprice = Mage::getModel('catalog/product')->load($_product->getId())->getSpecialPrice(); ?>
                            <?php if($specialprice) {?>
                            
                            <div class="price"> <?php echo $_formattedSpecialPrice = Mage::helper('core')->currency($_product->getSpecialPrice(),true,false); ?> <span class="not-now"> <?php echo $_formattedActualPrice = Mage::helper('core')->currency($_product->getPrice(),true,false); ?> </span></div>
                            <?php } else {  ?>
                            <div class="price"> <?php echo $_formattedActualPrice = Mage::helper('core')->currency($_product->getPrice(),true,false); ?></div>
                            <?php } ?>
                        </div>
                    </a>
                    <div class="actions">
                        <?php if(!$_product->canConfigure() && $_product->isSaleable()): ?>
                    <button type="button" title="<?php echo $this->quoteEscape($this->__('Add to Cart')) ?>" class="button btn-cart" onclick="setLocation('<?php echo $this->getAddToCartUrl($_product) ?>')"><span><span><?php echo $this->__('Add to Cart') ?></span></span></button>
                        <?php elseif($_product->getStockItem() && $_product->getStockItem()->getIsInStock()): ?>
                        <button type="button" title="<?php echo $this->quoteEscape($this->__('Add to Cart')) ?>" class="button" href="<?php echo $_product->getProductUrl() ?>"><span><span><?php echo $this->__('Add to Cart') ?></span></span></button>
                        <?php else: ?>
                        <p class="availability out-of-stock"><span><?php echo $this->__('Out of stock') ?></span></p>
                        <?php endif; ?>
                    </div>
                    </div>
                <?php //endif ?>
        <?php endforeach ?>