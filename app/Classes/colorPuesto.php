<?php
/**
 * RandomColor 1.0.4
 *
 * PHP port of David Merfield JavaScript randomColor
 * https://github.com/davidmerfield/randomColor
 *
 *
 * The MIT License (MIT)
 *
 * Copyright (c) 2014 Damien "Mistic" Sorel
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

namespace App\Classes;

use Carbon\Carbon;
use Auth;

class colorPuesto
{
    static function colores($reserva, $asignado_usuario, $asignado_miperfil,$asignado_otroperfil,$puesto,$origen="Reservas",$fecha){
        $tam_borde=$puesto->border!=null?$puesto->border:(isMobile()?$puesto->factor_puestob-1:$puesto->factor_puestob);
        $borde=$puesto->val_color?$puesto->val_color:$puesto->hex_color;
        
        //Respuesta buena
        $respuesta= [
            'color'=>"#dff9d2",
            'font_color'=>"#444",
            'clase_disp'=>"disponible",
            'title'=>"Disponible",
            'borde'=>"border: ".$tam_borde."px solid ".$borde.";",
            'border-radius'=>$puesto->factor_puestor."px",
            "transp"=>1
        ];

        //Primero vemos si el puesto esta reservado, asignado a persona o perfil
        if(isset($reserva)){
            
            if(isset($reserva->fec_fin_reserva)){
                $horas_reserva="de ".Carbon::parse($reserva->fec_reserva)->format('H:i')." a ".Carbon::parse($reserva->fec_fin_reserva)->format('H:i'); 
            } else {
                $horas_reserva="";
            }
            try{
                $usuario_usando="P";
                if(session('CL')['mca_mostrar_nombre_usando']=='S'){
                    $usuario_usando="por ".$reserva->name;
                }
            } catch(\Exception $e){
                $usuario_usando="";
            }
            $respuesta= [
                'color'=>"LightCoral",
                'font_color'=>"#fff",
                'clase_disp'=>"",
                'title'=>"Reservado ".$usuario_usando." de ".Carbon::parse($reserva->fec_reserva)->format('H:i')." a ".Carbon::parse($reserva->fec_fin_reserva)->format('H:i'),
                'borde'=>"border: ".$tam_borde."px solid ".$borde.";",
                'border-radius'=>$puesto->factor_puestor."px",
                "transp"=>0.8
            ];
        } 
        if(isset($asignado_usuario)){
            $respuesta= [
                'color'=>"#f2cb07",
                'font_color'=>"#fff",
                'clase_disp'=>"",
                'title'=>"Puesto permanentemente asignado a ".$asignado_usuario->name,
                'borde'=>"border: ".$tam_borde." solid #ff9f1a;",
                'border-radius'=>$puesto->factor_puestor."px",
                "transp"=>0.8
            ];
        } 
        if(isset($asignado_otroperfil)){
            $respuesta= [
                'color'=>"#e8c468",
                'font_color'=>"#fff",
                'clase_disp'=>"",
                'title'=>"Puesto reservado para  ".$asignado_otroperfil->des_nivel_acceso??'',
                'borde'=>"",
                'border-radius'=>$puesto->factor_puestor."px",
                "transp"=>0.8
            ];
        } 
        if(isset($asignado_miperfil)){
            $respuesta= [
                'color'=>"#dff9d2",
                'font_color'=>"#05688f",
                'clase_disp'=>"disponible",
                'title'=>"Puesto reservado para  ".$asignado_miperfil->des_nivel_acceso??'',
                'borde'=>"border: ".$tam_borde." solid #05688f;",
                'border-radius'=>$puesto->factor_puestor."px",
                "transp"=>1
            ];
            
        }   

        //Por ultimo si esta en uso
        if($puesto->id_estado==2){  //En uso
            try{
                $usuario_usando="";
                if(session('CL')['mca_mostrar_nombre_usando']=='S'){
                    $usuario_usando="por ".$puesto->usuario_usando;
                    if(isset($reserva)){
                        $usuario_usando.=chr(10).chr(13)." Reservado por ".$reserva->name;
                    }
                }
            } catch(\Exception $e){
                $usuario_usando="";
            }
           
            if($fecha==Carbon::now()->format('d/m/Y'))//Asumimos que todos los dias se resetearan los puestos, por lo que si es una fecha dsitinta de hoy no mostramos el puesto como ocupado
            {
                $respuesta= [
                    'color'=>"#ef253c",
                    'font_color'=>"#fff",
                    'clase_disp'=>"",
                    'title'=>"Puesto en uso ".$usuario_usando,
                    'borde'=>"",
                    'border-radius'=>$puesto->factor_puestor."px",
                    "transp"=>0.8
                ];
            }
        } 

        ///////Estados de incidencia, bloqueo, encuesta, etc.... si alguno de estos esta, da igual el estado del puesto, se muestra el color de la incidencia
       

        if ($puesto->mca_incidencia=='S'){  //Incidencia
            $respuesta= [
                'color'=>"#ffb300",
                'font_color'=>"#fff",
                'clase_disp'=>"",
                'title'=>"<span class='text-warning'>(<i class='fa-solid fa-triangle-exclamation text-warning'></i> Puesto con incidencia)</span>",
                'borde'=>"border: 3px solid #f00;",
                'border-radius'=>$puesto->factor_puestor."px",
                "transp"=>0.8
            ];
        } 
        if($puesto->id_estado==5){  //Bloqueado
            $respuesta= [
                'color'=>"#3a444e",
                'font_color'=>"#fff",
                'clase_disp'=>"",
                'title'=>"Puesto bloqueado",
                'borde'=>"",
                'border-radius'=>$puesto->factor_puestor."px",
                "transp"=>0.8
            ];
        } 
        if($puesto->id_estado==4){  //Inoperativo
            $respuesta= [
                'color'=>"#3a444e",
                'font_color'=>"#fff",
                'clase_disp'=>"",
                'title'=>"Puesto inoperativo",
                'borde'=>"",
                'border-radius'=>$puesto->factor_puestor."px",
                "transp"=>0.8
            ];
        } 
        if($puesto->id_estado==3 && session('CL')['mca_limpieza']=='S'){  //Limpieza
            $respuesta= [
                'color'=>"#00a4e6",
                'font_color'=>"#fff",
                'clase_disp'=>"",
                'title'=>"Puesto pendiente de limpieza",
                'borde'=>"",
                'border-radius'=>$puesto->factor_puestor."px",
                "transp"=>0.8
            ];
        } 
        if($puesto->id_estado==7){  //No usabble
            $respuesta= [
                'color'=>"#9bb3bf",
                'font_color'=>"#000",
                'clase_disp'=>"",
                'title'=>"No usable (encuesta)",
                'borde'=>"",
                'border-radius'=>$puesto->factor_puestor."px",
                "transp"=>0.8
            ];
        } 
        
    return $respuesta;
    }

    
    
}