<?php
namespace src\models;
use \core\Model;

class OrdersStatu extends Model {
    const EM_ABERTO = 1;
    const AGUARDANDO_PAGAMENTO = 2;
    const PAGO = 3;
    const ENTREGUE = 4;
    
}