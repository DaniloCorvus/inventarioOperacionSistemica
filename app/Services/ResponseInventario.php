<?php

namespace App\Services;
use Illuminate\Support\Facades\DB;
use App\Models\Inventario;

class ResponseInventario
{

    public function index()
    {
       $query = '
        SELECT  p.num_parte,
                p.codigo,
                p.cod_interno,
                p.modelo,
                cl.nombre as cellar,
                i.serie,
                i.cantidad_disponible,
                i.id,

                IFNULL((
                    SELECT costo_venta
                    FROM  cargues_inventario c
                    where c.inventario_id = i.id
                    ORDER BY c.created_at DESC LIMIT 1
                ),0) AS costo_venta

                FROM inventario i
                INNER JOIN productox p on p.id = i.productox_id
                INNER JOIN cellars cl on cl.id = i.cellar_id

        ';
        $res = DB::select($query);
        #dd($res);
        return datatables($res)
            ->editColumn('action', function ($Inventario) {
                $button =  '<div class="text-lg-right text-nowrap">';
                $button .=
                    '<a class="btn btn-circle btn-primary mr-1" href="/cargue-inventario/' . $Inventario->id . '" 
                    title="VerHistorial">
                    <i class="fa fa-eye"></i>
                    </a>';
                $button .= '</div>';
                return $button;
            })
            ->rawColumns(['action'])
            ->addIndexColumn()
            ->toJson();
    }
}
