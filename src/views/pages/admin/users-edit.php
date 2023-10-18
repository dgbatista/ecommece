<?php $render('admin/header', ['pageActive' => $pageActive, 'user'=>$user]); ?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>
    Lista de Usuários
  </h1>
    <?php if(!empty($flash)): ?>
      <div class="alert alert-danger" role="alert"><?php echo $flash; ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
    <?php endif;?>
  <ol class="breadcrumb">
    <li><a href="<?=$base;?>/admin"><i class="fa fa-dashboard"></i> Home</a></li>
    <li><a href="<?=$base;?>/admin/users">Usuários</a></li>
    <li class="active"><a href="<?=$base;?>/admin/users/create">Cadastrar</a></li>
  </ol>
</section>

<!-- Main content -->
<section class="content">

  <div class="row">
  	<div class="col-md-12">
  		<div class="box box-success">
        <div class="box-header with-border">
          <h3 class="box-title">Editar Usuário</h3>
        </div>
        <!-- /.box-header -->
        <!-- form start -->
        <form role="form" action="<?=$base?>/admin/users/update" method="post">
          <div class="box-body">
            <div><input type="hidden" name="iduser" value="<?=$user->iduser?>" /></div>
            <div class="form-group">
              <label for="desperson">Nome *</label>
              <input type="text" class="form-control" id="desperson" name="desperson" placeholder="Digite o nome" value="<?=$user->desperson;?>">
            </div>
            <div class="form-group">
              <label for="deslogin">Login *</label>
              <input type="text" class="form-control" id="deslogin" name="deslogin" placeholder="Digite o login" value="<?=$user->deslogin;?>">
            </div>
            <div class="form-group">
              <label for="nrphone">Telefone *</label>
              <input type="tel" class="form-control" id="nrphone" name="nrphone" placeholder="Digite o telefone" value="<?=$user->nrphone;?>">
            </div>
            <div class="form-group">
              <label for="desemail">E-mail *</label>
              <input type="email" class="form-control" id="desemail" name="desemail" placeholder="Digite o e-mail" value="<?=$user->desemail?>">
            </div>
            <div class="form-group">
              <label for="despassword">Senha *</label>
              <input type="password" class="form-control" id="despassword" name="despassword" placeholder="Digite a senha">
            </div>
            <div class="checkbox">
              <label>
                <input type="checkbox" name="inadmin" value="<?=$user->inadmin;?>" <?php echo ($user->inadmin == 1)? 'checked': '';?>/> Acesso de Administrador
              </label>
            </div>
          </div>
          <!-- /.box-body -->
          <div class="box-footer">
            <button type="submit" class="btn btn-success">Salvar</button>
          </div>
        </form>
      </div>
  	</div>
  </div>

</section>
<!-- /.content -->
</div>
<!-- /.content-wrapper -->

<?php $render('admin/footer');?>