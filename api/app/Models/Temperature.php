<?php

namespace App\Models;

class Temperature {
    public int $temp;
    public int $temp_min;
    public int $temp_max;
    public int $pressure;
    public int $humidity;

    public function __construct(object $data = null)
    {
        if($data != null)
        {
            $this->temp     = $data->temp;
            $this->temp_max = $data->temp_min;
            $this->temp_max = $data->temp_max;
            $this->pressure = $data->pressure;
            $this->humidity = $data->humidity;
        }
    }
}
