<?=$render('site/header', [
    'loggedUser' => $loggedUser ?: false,
	'qtd_itens' => $cart[1]['total'],
    'total_cart'=> $cart[1]['freight']['total']]); ?>

<div class="single-product-area">
    <div class="zigzag-bottom"></div>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                
                <h1>Pagamento N° <?=$order->idorder;?></h1>

                <button type="submit" id="btn-print" class="button alt" style="margin-bottom:10px">Imprimir</button>

                <iframe src="<?=$base;?>/boleto/<?=$order->idorder;?>" name="boleto" frameborder="0" style="width:100%; min-height:1000px; border:1px solid #CCC; padding:20px;"></iframe>

                <script>
                document.querySelector("#btn-print").addEventListener("click", function(event){

                    event.preventDefault();

                    window.frames["boleto"].focus();
                    window.frames["boleto"].print();

                });                
                </script>

            </div>
        </div>
    </div>
</div>

<?php print_r($cart);?>