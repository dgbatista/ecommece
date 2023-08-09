<?php $render('admin/header', ['pageActive' => $pageActive]); ?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>
    Lista de Produtos
  </h1>
  <ol class="breadcrumb">
    <li><a href="/admin"><i class="fa fa-dashboard"></i> Home</a></li>
    <li class="active"><a href="/admin/$products">Produtos</a></li>
  </ol>
</section>

<!-- Main content -->
<section class="content">

  <div class="row">
  	<div class="col-md-12">
  		<div class="box box-primary">
            
            <div class="box-header">
              <a href="<?=$base;?>/admin/products/create" class="btn btn-success">Cadastrar Produto</a>
            </div>

            <div class="box-body no-padding">
              <table class="table table-striped">
                <thead>
                  <tr>
                    <th style="width: 10px">#</th>
                    <th>Nome da Produto</th>
                    <th>Preço</th>
                    <th>Largura</th>
                    <th>Altura</th>
                    <th>Comprimento</th>
                    <th>Peso</th>
                    <th style="width: 140px">&nbsp;</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach($products as $product): ?>
                    <tr>
                      <td><?=$product->idproduct;?></td>
                      <td><?=$product->desproduct;?></td>
                      <td><?=$product->vlprice;?></td>
                      <td><?=$product->vlwidth;?></td>
                      <td><?=$product->vlheight;?></td>
                      <td><?=$product->vllength;?></td>
                      <td><?=$product->vlweight;?></td>
                      <td>
                        <a href="<?=$base;?>/admin/products/<?=$product->idproduct;?>" class="btn btn-primary btn-xs"><i class="fa fa-edit"></i> Editar</a>
                        <a href="<?=$base;?>/admin/products/<?=$product->idproduct;?>/delete" onclick="return confirm('Deseja realmente excluir este registro?')" class="btn btn-danger btn-xs"><i class="fa fa-trash"></i> Excluir</a>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
            <!-- /.box-body -->
          </div>
  	</div>
  </div>

</section>
<!-- /.content -->
</div>
<!-- /.content-wrapper -->

<?php $render('admin/footer');?>