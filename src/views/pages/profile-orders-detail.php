<?=$render('site/header', [
	'loggedUser' => $loggedUser ?? false,
	'qtd_itens' => $cart[1]['total'],
    'total_cart'=> $cart[1]['freight']['total']
]);?>

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
                                    <?=$product->desproduct?> <strong class="product-quantity">× <?=$product->qtd_products?></strong> 
                                </td>
                                <td class="product-total">
                                    <span class="amount">R$<?=$product->vlprice;?></span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr class="cart-subtotal">
                                <th>Subtotal</th>
                                <td><span class="amount"><?=($order->vltotal) ? 'R$ '.$order->vltotal : ''?></span>
                                </td>
                            </tr>
                            <tr class="shipping">
                                <th>Frete</th>
                                <td>
                                    <?=($order->vlfreight) ? 'R$ '.$order->vlfreight : ''?> 
                                    <?=($order->nrdays) ? '<small>prazo de '.$order->nrdays.' dia(s)</small>' : '';?>
                                    <input type="hidden" class="shipping_method" value="free_shipping" id="shipping_method_0" data-index="0" name="shipping_method[0]">
                                </td>
                            </tr>
                            <tr class="order-total">
                                <th>Total do Pedido</th>
                                <td><strong><span class="amount">
                                    <?php if($order->vltotal): ?>
                                        <span class="amount">                                                        
                                            R$ <?=($order->vltotal + $order->vlfreight)?>
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