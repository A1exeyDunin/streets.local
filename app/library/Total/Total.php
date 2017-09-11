<?php

namespace Total;

use Buildings;


class Total
{


    /**
     * @param $id
     * @return Buildings|Buildings[]|int|\Phalcon\Mvc\Model\ResultSetInterface
     * Возвращает количество строений для той или иной улицы
     */
    public function getTotal($id)
    {
        $total = Buildings::find("street_id = $id");
        $total = count($total);

        return $total;
    }
}

