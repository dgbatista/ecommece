<?php $render('site/header', ['menuCurrent' => $menuCurrent]); ?>

<div class="product-big-title-area">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="product-bit-title text-center">
                    <h2>Carrinho de Compras</h2>
                </div>
            </div>
        </div>
    </div>
</div> <!-- End Page title area -->

<div class="single-product-area">
    <div class="zigzag-bottom"></div>
    <div class="container">
        <div class="row">
            
            <div class="col-md-12">
                <div class="product-content-right">
                    <div class="woocommerce">

                        <form action="/checkout">
                            
                            <?php if(!empty($flash)): ?>
                                <div class="alert alert-danger" role="alert">
                                 <?=$flash;?>
                                </div>
                            <?php endif; ?>

                            <table cellspacing="0" class="shop_table cart">
                                <thead>
                                    <tr>
                                        <th class="product-remove">&nbsp;</th>
                                        <th class="product-thumbnail">&nbsp;</th>
                                        <th class="product-name">Produto</th>
                                        <th class="product-price">Preço</th>
                                        <th class="product-quantity">Quantidade</th>
                                        <th class="product-subtotal">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php //echo '<pre>'; print_r($products); ?>
                                    
                                    <?php foreach($products as $product):?>                                        
                                        <tr class="cart_item">
                                            <td class="product-remove">
                                                <a title="Remove this item" class="remove" href="<?=$base;?>/cart/<?=$product->idproduct?>/remove">×</a> 
                                            </td>

                                            <td class="product-thumbnail">
                                                <a href="<?=$base;?>/products/<?=$product->desurl;?>"><img width="145" height="145" alt="poster_1_up" class="shop_thumbnail" src="<?=$base?>/assets/site/img/products/<?=$product->idproduct.'.jpg'?>"></a>
                                            </td>

                                            <td class="product-name">
                                                <a href="<?=$base;?>/products/<?=$product->desurl;?>"><?=$product->desproduct?></a> 
                                            </td>

                                            <td class="product-price">
                                                <span class="amount">R$<?=$product->vlprice?></span> 
                                            </td>

                                            <td class="product-quantity">
                                                <div class="quantity buttons_added">
                                                    <input type="button" class="minus" value="-" onclick="window.location.href = '<?=$base;?>/cart/<?=$product->idproduct;?>/minus'">
                                                    <input type="number" size="4" class="input-text qty text" title="Qty" value="<?=$qtd[$product->idproduct]?>" min="0" step="1" disabled/>
                                                    <input type="button" class="plus" value="+" onclick="window.location.href = '<?=$base;?>/cart/<?=$product->idproduct;?>/add'">
                                                </div>
                                            </td>

                                            <td class="product-subtotal">
                                                <span class="amount">R$<?=$product->total;?></span> 
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                    
                                </tbody>
                            </table>

                            <div class="cart-collaterals">

                                <div class="cross-sells">

                                    <h2>Cálculo de Frete</h2>
                                    
                                    <div class="coupon">
                                        <label for="cep">CEP:</label>
                                        <input type="text" placeholder="00000-000" id="cep" class="input-text" name="zipcode" value="">
                                        <input type="submit" formmethod="post" formaction="<?=$base;?>/cart/freight" value="CÁLCULAR" class="button">
                                    </div>

                                </div>

                                <div class="cart_totals ">

                                    <h2>Resumo da Compra</h2>

                                    <table cellspacing="0">
                                        <tbody>
                                            <tr class="cart-subtotal">
                                                <th>Subtotal</th>
                                                <td><span class="amount">$700.00</span></td>
                                            </tr>

                                            <tr class="shipping">
                                                <th>Frete</th>
                                                <td>R$<?=$vlfreight;?> <small>prazo de 0 dia(s)</small></td>
                                            </tr>

                                            <tr class="order-total">
                                                <th>Total</th>
                                                <td><strong><span class="amount">$705.00</span></strong> </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                            </div>

                            <div class="pull-right">
                                <input type="submit" value="Finalizar Compra" name="proceed" class="checkout-button button alt wc-forward">
                            </div>

                        </form>

                    </div>                        
                </div>                    
            </div>
        </div>
    </div>
</div>

<?php $render('site/footer'); ?>