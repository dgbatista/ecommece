<?php $render('admin/header', ['pageActive' => $pageActive, 'user'=>$user]); ?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>
    Lista de Pedidos
  </h1>
  <ol class="breadcrumb">
    <li><a href="/admin"><i class="fa fa-dashboard"></i> Home</a></li>
    <li class="active"><a href="/admin/orders">Pedidos</a></li>
  </ol>
</section>

<!-- Main content -->
<section class="content">

  <div class="row">
  	<div class="col-md-12">
  		<div class="box box-primary">

            <div class="box-body no-padding">
              <table class="table table-striped">
                <thead>
                  <tr>
                    <th style="width: 10px">#</th>
                    <th>Cliente</th>
                    <th>Valor Total</th>
                    <th>Valor do Frete</th>
                    <th>Status</th>
                    <th style="width: 220px">&nbsp;</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if($orders) :?>
                    <?php foreach($orders as $order): ?>
                    <tr>
                      <td><?=$order->idorder;?></td>
                      <td><?=$order->desperson;?></td>
                      <td><?='R$'.$order->vltotal;?></td>
                      <td><?='R$'.$order->vlfreight;?></td>
                      <td><?=$order->desstatus;?></td>
                      <td>
                        <a href="<?=$base;?>/admin/orders/<?=$order->idorder;?>" class="btn btn-default btn-xs"><i class="fa fa-search"></i> Detalhes</a>
                        <a href="<?=$base;?>/admin/orders/<?=$order->idorder;?>/status" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i> Status</a>
                        <a href="<?=$base;?>/admin/orders/<?=$order->idorder;?>/delete" onclick="return confirm('Deseja realmente excluir este registro?')" class="btn btn-danger btn-xs"><i class="fa fa-trash"></i> Excluir</a>
                      </td>
                    </tr>
                    <?php endforeach; ?>
                  <?php else :?>
                  <tr>
                      <td colspan="6">Nenhum pedido foi encontrado.</td>
                  </tr>
                  <?php endif; ?>
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

<?=$render('admin/footer');?>
