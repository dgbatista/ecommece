<?=$render('site/header', [
	'loggedUser' => $loggedUser ?? false,
	'qtd_itens' => $cart[1]['total'],
    'total_cart'=> $cart[1]['freight']['total']
]);?>

<?php echo '<pre>'; print_r($order); echo '</pre>';?>
<?php echo '<pre>'; print_r($cartProducts); echo '</pre>';?>
<?php echo '<pre>'; print_r($cart); echo '</pre>';?>



<style>
@media print {
    .header-area,
    .site-branding-area,
    .sticky-wrapper,
    .footer-top-area,
    .footer-bottom-area,
    .single-product-area .col-md-3,
    .button.alt,
    .product-big-title-area {
        display:none!important;
    }
    .single-product-area .col-md-9 {
        width: 100%!important;
    }
}
</style>

<div class="product-big-title-area">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="product-bit-title text-center">
                    <h2>Minha Conta</h2>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="single-product-area">
    <div class="zigzag-bottom"></div>
    <div class="container">
        <div class="row">                
            <div class="col-md-3">
                <?=$this->render('profile-menu');?>
            </div>
            <div class="col-md-9">
                
                <h3 id="order_review_heading" style="margin-top:30px;">Detalhes do Pedido N°<?=$order->idorder;?></h3>
                <div id="order_review" style="position: relative;">
                    <table class="shop_table">
                        <thead>
                            <tr>
                                <th class="product-name">Produto</th>
                                <th class="product-total">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach($cartProducts as $product):?> 
                            <tr class="cart_item">
                                <td class="product-name">
                                    <?=$product->desproduct?> <strong class="product-quantity">× {$value.nrqtd}</strong> 
                                </td>
                                <td class="product-total">
                                    <span class="amount">R${$value.vltotal}</span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr class="cart-subtotal">
                                <th>Subtotal</th>
                                <td><span class="amount"><?=($cart[1]['freight']['total']) ? 'R$ '.$cart[1]['freight']['total'] : ''?></span>
                                </td>
                            </tr>
                            <tr class="shipping">
                                <th>Frete</th>
                                <td>
                                    <?=($cart[0]->vlfreight) ? 'R$ '.$cart[0]->vlfreight : '';?> 
                                    <?=($cart[0]->nrdays != '') ? '<small>prazo de '.$cart[0]->nrdays.' dia(s)</small>' : '';?>
                                    <input type="hidden" class="shipping_method" value="free_shipping" id="shipping_method_0" data-index="0" name="shipping_method[0]">
                                </td>
                            </tr>
                            <tr class="order-total">
                                <th>Total do Pedido</th>
                                <td><strong><span class="amount">
                                    <?php if($cart[1]['freight']['total']): ?>
                                        <span class="amount">                                                        
                                            R$ <?=($cart[1]['freight']['total']+ $cart[0]->vlfreight)?>
                                        </span>
                                    <?php endif; ?>
                                </span></strong> </td>
                            </tr>
                        </tfoot>
                    </table>
                    <div id="payment">
                        <div class="form-row place-order">
                            <input type="submit" value="Imprimir" class="button alt" onclick="window.print()">
                        </div>
                        <div class="clear"></div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<?=$render('site/foorter')?>