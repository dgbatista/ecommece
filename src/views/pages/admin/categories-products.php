<?php $render('admin/header', ['pageActive' => $pageActive]); ?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>
    Produtos da Categoria <?=$category->descategory;?>
  </h1>
  <ol class="breadcrumb">
    <li><a href="<?=$base;?>/admin"><i class="fa fa-dashboard"></i> Home</a></li>
    <li><a href="<?=$base;?>/admin/categories">Categorias</a></li>
    <li><a href="<?=$base;?>/admin/categories/<?=$category->idcategory;?>"><?=$category->descategory;?></a></li>
    <li class="active"><a href="<?=$base;?>/admin/categories/<?=$category->idcategory;?>/products">Produtos</a></li>
  </ol>
</section>

<!-- Main content -->
<section class="content">

    <div class="row">
        <div class="col-md-6">
            <div class="box box-primary">
                <div class="box-header with-border">
                <h3 class="box-title">Todos os Produtos</h3>
                </div>
                <!-- /.box-header -->
                <!-- form start -->
                <div class="box-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                            <th style="width: 10px">#</th>
                            <th>Nome do Produto</th>
                            <th style="width: 240px">&nbsp;</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($productsNotRelated as $product): ?>
                                <tr>
                                <td><?=$product['idproduct']?></td>
                                <td><?=$product['desproduct']?></td>
                                <td>
                                    <a href="<?=$base;?>/admin/categories/<?=$category->idcategory;?>/products/<?=$product['idproduct']?>/add" class="btn btn-primary btn-xs pull-right"><i class="fa fa-arrow-right"></i> Adicionar</a>
                                </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="box box-success">
                <div class="box-header with-border">
                <h3 class="box-title">Produtos na Categoria <?=$category->descategory?></h3>
                </div>
                <!-- /.box-header -->
                <!-- form start -->
                <div class="box-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                            <th style="width: 10px">#</th>
                            <th>Nome do Produto</th>
                            <th style="width: 240px">&nbsp;</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($productsRelated as $product): ?>
                            <tr>
                                <td><?=$product['idproduct']?></td>
                                <td><?=$product['desproduct']?></td>
                                <td>
                                <a href="<?=$base;?>/admin/categories/<?=$category->idcategory;?>/products/<?=$product['idproduct']?>/remove" class="btn btn-primary btn-xs pull-right"><i class="fa fa-arrow-right"></i> Remover</a>
                                </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>        
    </div>

</section>
<!-- /.content -->
</div>
<!-- /.content-wrapper -->

<?php $render('admin/footer')?>