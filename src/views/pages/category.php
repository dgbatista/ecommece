<?php $render('site/header');?>

<div class="product-big-title-area">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="product-bit-title text-center">
                    <h2><?=$category->descategory;?></h2>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="single-product-area">
    <div class="zigzag-bottom"></div>
    <div class="container">
        <div class="row">

            <?php foreach ($products['products'] as $item):?>

                <div class="col-md-3 col-sm-6">
                    <div class="single-shop-product">
                        <div class="product-upper">
                            <img src="<?=$base?>/assets/site/img/products/<?=$item['idproduct']?>.jpg" alt="">
                        </div>
                        <h2><a href="<?=$base;?>/products/<?=$item['desurl']?>"><?=$item['desproduct']?></a></h2>
                        <div class="product-carousel-price">
                            <ins>R$<?=$item['vlprice']?></ins> <!--<del>$999.00</del> -->
                        </div>  
                        
                        <div class="product-option-shop">
                            <a class="add_to_cart_button" data-quantity="1" data-product_sku="" data-product_id="70" rel="nofollow" href="/canvas/shop/?add-to-cart=70">Comprar</a>
                        </div>                       
                    </div>
                </div>

            <?php endforeach; ?>

        </div>

        Page Count:<?=$products['pageCount'];?>
        
        <div class="row">
            <div class="col-md-12">
                <div class="product-pagination text-center">
                    <nav>
                        <ul class="pagination">
                        <li>
                            <a href="<?=$base;?>/categories/<?=$category->idcategory;?>?page=<?=(($page-1) < 0)? 0 : $page-1 ;?>" aria-label="Previous">
                            <span aria-hidden="true">«</span>
                            </a>
                        </li>
                            <?php for($q=0; $q< $products['pageCount']; $q++):?>
                                <li><a href="<?=$base;?>/categories/<?=$category->idcategory;?>?page=<?=$q;?>"><?=$q+1;?></a></li>
                            <?php endfor; ?>
                        <li>
                            <a href="<?=$base;?>/categories/<?=$category->idcategory;?>?page=<?=(($page+1) >= $products['pageCount'])?$page:$page+1 ;?>" aria-label="Next">
                            <span aria-hidden="true">»</span>
                            </a>
                        </li>
                        </ul>
                    </nav>                        
                </div>
            </div>
        </div>
    </div>
</div>

<?php $render('site/footer');?>