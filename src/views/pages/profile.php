<?=$render('site/header', ['loggedUser'=>$loggedUser]);?>

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
                <?php $this->render('profile-menu');?>
            </div>
            <div class="col-md-9">
                <?php if ($profileMsg != ''): ?>
                    <div class="alert alert-success">
                        <?=$profileMsg;?>
                    </div>
                <?php endif;?>
                <?php if ($profileError != ''): ?>
                    <div class="alert alert-danger">
                        <?=$profileError;?>
                    </div>
                <?php endif;?>                
                <form method="post" action="<?=$base;?>/profile">
                    <div class="form-group">
                    <label for="desperson">Nome completo</label>
                    <input type="text" class="form-control" id="desperson" name="desperson" placeholder="Digite o nome aqui" value="<?=$loggedUser->desperson?>">
                    </div>
                    <div class="form-group">
                    <label for="desemail">E-mail</label>
                    <input type="email" class="form-control" id="desemail" name="desemail" placeholder="Digite o e-mail aqui" value="<?=$loggedUser->desemail?>">
                    </div>
                    <div class="form-group">
                    <label for="nrphone">Telefone</label>
                    <input type="tel" class="form-control" id="nrphone" name="nrphone" placeholder="Digite o telefone aqui" value="<?=$loggedUser->nrphone?>">
                    </div>
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?=$render('site/footer');?>