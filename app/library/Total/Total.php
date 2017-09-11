<?php

namespace Total;

use Buildings;


class Total
{




    public function getTotal($id)
    {
        $total = Buildings::find("street_id = $id");
        $total = count($total);

        return $total;
    }
}

