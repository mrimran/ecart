<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE_AFL.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    design
 * @package     rwd_default
 * @copyright   Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license     http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
/**
 * Product view template
 *
 * @see Mage_Catalog_Block_Product_View
 * @see Mage_Review_Block_Product_View
 */
?>
<?php $_helper = $this->helper('catalog/output'); ?>
<?php $_product = $this->getProduct(); ?>
<script type="text/javascript">
    var optionsPrice = new Product.OptionsPrice(<?php echo $this->getJsonConfig() ?>);
</script>

<div id="messages_product_view"><?php echo $this->getMessagesBlock()->toHtml() ?></div>
<div class="__product__main__area">
  <div class="row">
    <form action="<?php echo $this->getSubmitUrl($_product, array('_secure' => $this->_isSecure())) ?>" method="post" id="product_addtocart_form"<?php if ($_product->getOptions()): ?> enctype="multipart/form-data"<?php endif; ?>>
      <?php echo $this->getBlockHtml('formkey') ?>
      <div class="no-display">
        <input type="hidden" name="product" value="<?php echo $_product->getId() ?>" />
        <input type="hidden" name="related_product" id="related-products-field" value="" />

        <input type="hidden" name="color" id="product-color-field" value="" />

      </div>
      <div class="col-sm-6"> <?php echo $this->getChildHtml('media') ?> </div>
      <div class="col-sm-6">
      <h1><?php echo $_helper->productAttribute($_product, $_product->getName(), 'name') ?> </h1>
      <?php if( $_product->getTypeId() != 'grouped' ): ?>            
        <?php $_helper = $this->helper('catalog/output'); ?>
        <?php $_brands = $this->getBrand($_product->getId()); ?>
        <?php if(!$_brands->count()): ?>  
        <div class="__type">
        <?php else :?>
        <div class="__type">by
        <?php foreach ($_brands as $brand): ?>
        <a href="#"><?php echo $brand->name ?></a>,
        <?php endforeach ?>
        <?php endif ?>
      <?php $_category_detail = Mage::registry('current_category'); ?>
        <?php if($_category_detail != ''){ ?>
        <a href="<?php echo $_category_detail->getUrl($_category); ?>"><?php echo $_category_detail->getName(); ?></a></div>
        <?php } else{ 

          $currentCatIds = $_product->getCategoryIds();
          if(count($currentCatIds) <= 0 ):?>
             <a href=""></a></div>
          <?php else :
          $categoryCollection = Mage::getResourceModel('catalog/category_collection')
                     ->addAttributeToSelect('name')
                     ->addAttributeToSelect('url')
                     ->addAttributeToFilter('entity_id', $currentCatIds)
                     ->addIsActiveFilter();                     
                     foreach($categoryCollection as $cat){ ?>
                          <a href="<?php echo $cat->getUrl($_category); ?>"><?php echo $cat->getName(); ?></a></div>

                  <?php  break; } ?>
                <?php endif ?>
          <?php } ?>
          <?php endif ?>

      <?php echo $this->getReviewsSummaryHtml($_product, "short", true)?> <?php echo $this->getChildHtml('product_review') ?>
      <?php
$_compareUrl = $this->helper('catalog/product_compare')->getAddUrl($_product);
?>
<?php if($_compareUrl) : ?>
<a href="<?php echo $_compareUrl ?>" class="link-compare"><?php echo $this->__('Add to Compare') ?></a>
<?php endif; ?>
      <?php if($_product->getTypeId() != 'grouped' ): ?>
      <div class="row">
        <div class="col-sm-8">
          <div class="price">
                                <?php
                                echo $_formattedSpecialPrice = Mage::helper('core')->currency($_product->getFinalPrice(), true, false);
                                $_formattedActualPrice = Mage::helper('core')->currency($_product->getPrice(), true, false);
                                ?>
                                    <?php if ($_formattedActualPrice != $_formattedSpecialPrice): ?>
                                    <span class="not-now">
                                    <?php echo $_formattedActualPrice = Mage::helper('core')->currency($_product->getPrice(), true, false); ?>
                                    </span>
                            <?php endif; ?>
                            </div>
        </div>
        <div class="col-sm-4">
          <div class="__you_saved">You save <?php echo Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())->getSymbol(); ?>
            <?php
 $specialprice = $_priceInclTax = $this->getPrice($sign.$value['pricing_value'], true)+
 $this->getProduct()->getFinalPrice();
  
  $_priceExclTax = $this->getPrice($sign.$value['pricing_value'])+
  $this->getProduct()->getPrice();
                     if($specialprice < $_priceExclTax) { 
                     $yousave =  $_priceExclTax - $specialprice;
                     echo $yousave;
                     }
                     ?>
            ! </div>
        </div>
      </div>
      <?php endif; ?>
      <div class="product_type_data"> <?php echo $this->getChildHtml('product_type_data') ?> 
    <?php echo $this->getChildHtml('extrahint') ?>
      <?php echo $this->getChildHtml('product_type_availability'); ?> 
      <?php echo $this->getChildHtml('alert_urls') ?>
      
      </div>
       <?php if ($_product->isAvailable()): ?>
      <div class="__in_stock"><strong>AVAILABILITY:</strong> In Stock
        <?php echo (int) Mage::getModel('cataloginventory/stock_item')->loadByProduct($_product)->getQty()?>
        pcs</div>
       <?php endif; ?>
      <?php if($_product->condition): ?>
      <div class="__condition"><strong>
        <?php  echo  $_product->getResource()->getAttribute('condition')->getFrontendLabel();   ?>
        : </strong> <?php echo $_product->getResource()->getAttribute('condition')->getFrontend()->getValue($_product); ?> 
        </div>
        <br>
      <?php endif ?>
      <?php if($_product->warrenty): ?>
      <!-- <div class="__warrenty"><strong>
        <?php //echo  $_product->getResource()->getAttribute('warrenty')->getFrontendLabel();  ?>
        :</strong> <?php //echo $_product->getResource()->getAttribute('warrenty')->getFrontend()->getValue($_product); ?> </div> -->
      <?php else: ?>
      <br/>
      <?php endif ?>
      <!-- <div class="panel-group product-meta" id="accordion" role="tablist" aria-multiselectable="true"> -->
          <?php if ($_product->isSaleable() && $this->hasOptions()):?>
                    <?php echo $this->getChildChildHtml('container1', '', true, true) ?>
          <?php endif;?>
         <!-- </div> -->

            <?php //echo $this->getChildChildHtml('container1', '', true, true) ?>
          
            <?php //echo $this->getChildChildHtml('container2', '', true, true) ?>
            <div class="buttons">
              <div class="row">
              <?php $upcoming = $_product->getAttributeText('upcomingproduct') ;
              if($upcoming == 'No'){?>
              
                <div class="col-sm-9">
                  <button type="button" title="<?php echo $buttonTitle ?>" class="button btn-cart btn btn-default btn1 btn-block" onclick="productAddToCartForm.submit(this)"><span>ADD TO CART</span> <i class="fa fa-shopping-cart"></i></button>
                </div>
                <?php } ?>
                <div class="col-sm-3">
                  <?php $_product = $this->getProduct(); ?>
                  <?php $_wishlistSubmitUrl = $this->helper('wishlist')->getAddUrl($_product); ?>
                  <?php if ($this->helper('wishlist')->isAllow()) : ?>
                  <a href="<?php echo $_wishlistSubmitUrl ?>" onclick="productAddToCartForm.submitLight(this, this.href); return false;" class="link-wishlist btn btn-default btn1 btn-block wishlist"><i class="fa fa-heart"></i><span>WISHLIST</span></a>
                  <?php endif; ?>
                </div>
              </div>
            </div>
            
            <div class="__share__this"> 
            
            <?php /*?><a onclick="return showForm('social_share')" >Share this product</a> <?php */?>
            <?php echo $this->getLayout()->createBlock('sharingtool/share')->setTemplate('addthis/sharingtool/share.phtml')->toHtml(); ?>
            
            </div>
          
        </div>
  
    </form>

  <script type="text/javascript">

        //<![CDATA[
        var productAddToCartForm = new VarienForm('product_addtocart_form');
        productAddToCartForm.submit = function (button, url) {
            if (this.validator.validate()) {
                var form = this.form;
                var oldUrl = form.action;
                if (url) {
                    form.action = url;
                }
                var e = null;
                try {
                    this.form.submit();
                } catch (e) {
                }
                this.form.action = oldUrl;
                if (e) {
                    throw e;
                }
                if (button && button != 'undefined') {
                    button.disabled = true;
                }
            }
        }.bind(productAddToCartForm);
        productAddToCartForm.submitLight = function (button, url) {
            if (this.validator) {
                var nv = Validation.methods;
                delete Validation.methods['required-entry'];
                delete Validation.methods['validate-one-required'];
                delete Validation.methods['validate-one-required-by-name'];
                // Remove custom datetime validators
                for (var methodName in Validation.methods) {
                    if (methodName.match(/^validate-datetime-.*/i)) {
                        delete Validation.methods[methodName];
                    }
                }
                if (this.validator.validate()) {
                    if (url) {
                        this.form.action = url;
                    }
                    this.form.submit();
                }
                Object.extend(Validation.methods, nv);
            }
        }.bind(productAddToCartForm);
        //]]>
    </script> 
</div>

<?php 
echo $this->getLayout()->createBlock('cms/block')->setBlockId('productdetailpagefirststaticblock')->toHtml();  

?>
<div class="__product__description">
<div class="row">
  <div class="col-sm-12">
    <?php if ($_product->description): ?>
    <h2><?php echo $_product->getResource()->getAttribute('description')->getFrontendLabel(); ?></h2>
    <div id="__product__detail" class="__product__detail "> <?php echo $_product->getResource()->getAttribute('description')->getFrontend()->getValue($_product); ?>
      <?php endif ?>
    </div>
  </div>
  <div id="product_specs" class="col-sm-12 " style="display:none;">
    <h2>Specifications</h2>
    <?php
            $product_id = Mage::registry('current_product')->getId();
            $_product = Mage::getModel('catalog/product')->load($product_id);
            $collections = array();
            $attributesCollection = Mage::getResourceModel('catalog/product_attribute_collection');
            $attributesCollection->setAttributeGroupFilter(18);
            foreach ($attributesCollection as $attribute) {
                ?>
    <div class="row">
      <?php
                    if ($attribute->getFrontendInput() == 'select' || $attribute->getFrontendInput() == 'boolean') {
                        ?>
      <div class="col-sm-6"> <strong> <?php echo $attribute->getFrontendLabel(); ?> </strong> </div>
      <div class="col-sm-6">
        <?php
                            if (is_array($_product->getAttributeText($attribute->getAttributeCode()))) {
                                $a = explode(",", $_product->getAttributeText($attribute->getAttributeCode()));
                            }

                            echo $_product->getAttributeText($attribute->getAttributeCode());
                            ?>
      </div>
      <?php
                    } else {
                        if ($attribute->getFrontendInput() == "text")
                            ;
                        ?>
      <div class="col-sm-6"> <strong> <?php echo $attribute->getFrontendLabel(); ?> </strong> </div>
      <?php
                        if ($attribute->getAttributeCode() == 'color' || $attribute->getAttributeCode() == 'storagecapacity') {
                            $colors = $_product->getAttributeText($attribute->getAttributeCode());
                            echo "<div class='col-sm-6'>";
                            foreach ($colors as $name => $value):
                                echo $value . ",";
                            endforeach;
                            echo "</div>";
                        } else {
                            ?>
      <div class="col-sm-6"> <?php echo $_product->getData($attribute->getAttributeCode()); ?> </div>
      <?php
                    }
                }
                ?>
    </div>
    <?php } ?>
  </div>
</div>
</div>

<!-- Button trigger modal -->
<button type="button" id="discountmodal" class="btn" data-toggle="modal" data-target="#myModal" style="display:none;">Launch modal</button>

<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-body">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <div class="modalstyles">
      <h1 class="title">Get 30% Off</h1>
      <hr>
      <p>Sign Up for Newsletter & Promotions</p>
      <form action="<?php echo $this->getFormAction(); ?>" id="NewsletterPromotion" method="post">
      <input name="discount" id="discount" title="<?php echo Mage::helper('core')->quoteEscape(Mage::helper('contacts')->__('Discount')) ?>" value="<?php echo $this->escapeHtml($this->helper('contacts')->getUserName()) ?>" class="widthfull" type="text" placeholder="" />
      <input type="text" name="hideit" id="hideit" value="" style="display:none !important;" />
      <button type="submit" title="<?php echo Mage::helper('core')->quoteEscape(Mage::helper('contacts')->__('Subscribe')) ?>" class="btn1"><?php echo Mage::helper('contacts')->__('Subscribe') ?></button>
      </form>
      </div>
      </div>
    </div>
  </div>
</div>

<script>
jQuery(window).load(function() {
  jQuery('#discountmodal').click();
});

jQuery('.bxslider').bxSlider({
      pagerCustom: '#bx-pager'
    });
    // jQuery('#__product__detail').more({
    //   length:1099,
    //   ellipsisText: '',
    //   moreText: 'Read Full Description',
    //   lessText: 'Read Less Description'
    // });
    // jQuery('#product_specs').more({
    //   length:335,
    //   leaway:10,
    //   ellipsisText: '',
    //   moreText: 'See Full Specs',
    //   lessText: 'See Less Specs'
    // });
</script> 

<script>
function showForm(id){
Dialog.confirm($('social_share').innerHTML, {className:"alphacube", width:400});
}
</script>
<?php //echo $this->getChildHtml('upsell_products') ?>
<?php //echo $this->getChildHtml('product_additional_data') ?>