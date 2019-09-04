<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Caja\SiafcaIntranetBundle\Entity;

/**
 * Description of InformePDF
 *
 * @author usuario
 */
class InformePDF extends \TCPDF
{
    private $parametros;

    public function __construct($parametros) {

        parent::__construct();
        $this->parametros = $parametros;
        $this->setTitle( "Informe de liquidacion" );
        $this->setHeaderMargin(10);
        $this->SetFooterMargin(15);

        $this->setPrintHeader(true);
        $this->setPrintFooter(true);


        $this->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);

        $this->setFooterFont( array( 'helvetica', '', 8 ) );

        // set default monospaced font
        $this->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        // set image scale factor
        $this->setImageScale(PDF_IMAGE_SCALE_RATIO);

        // set JPEG quality
        $this->setJPEGQuality(75);

        //$this->startPageGroup();
    }

        function Header() {

        $this->SetFont('helvetica', 'B', 10);
        $border = 0;

        // Logo de la Provincia
        $file = 'images/logoProvincia.gif';
        $this->Image($file, $x=PDF_MARGIN_LEFT-5, $y=PDF_MARGIN_TOP-15, $w='', $h='', $type='GIF', $link='www.santafe.gov.ar', $align='', $resize=false, $dpi=150, $palign='', $ismask=false, $imgmask=false, $border=0, $fitbox=false, $hidden=false, $fitonpage=false);

        // Logo de la Caja
        $file = 'images/logoCaja.jpg';
        $this->Image($file, $x=130, $y=PDF_MARGIN_TOP-15, $w='', $h='', $type='JPG', $link='www.santafe.gov.ar', $align='', $resize=false, $dpi=150, $palign='', $ismask=false, $imgmask=false, $border=0, $fitbox=false, $hidden=false, $fitonpage=false);

        // Barra de encabezado
        $file = 'images/barra-header.png';
        $this->Image($file, $x=PDF_MARGIN_LEFT-5, $y=35, $w='190', $h='1', $type='PNG', $link='www.santafe.gov.ar', $align='', $resize=true, $dpi=150, $palign='', $ismask=false, $imgmask=false, $border=0, $fitbox=false, $hidden=false, $fitonpage=false);

        $this->SetXY(PDF_MARGIN_LEFT-5, 37);
        $this->SetFont('helvetica', '', 10);
        $this->Cell(40, 0, 'Período: ' . $this->parametros['periodo'], 0, '', 'L');

        $this->SetXY(PDF_MARGIN_LEFT+50, 37);
        $this->SetFont('helvetica', '', 10);
        $this->Cell(65, 0, 'Informe de Liquidación Nro: ' . $this->parametros['id'], 0, '0', 'C');

        $this->SetXY(PDF_MARGIN_LEFT+125, 37);
        $this->SetFont('helvetica', '', 10);
        $this->Cell(60, 0, 'Fecha y hora: ' . date('d/m/Y H:i:s'), 0, '0', 'R');

        }

        public function render( )    {
        $border = 1;
        $this->SetFontSize(7);

        $this->addPage();

        $codOrganismo = substr($this->parametros['id'], 0, 8);

        $est1 =array('width' => 0.1,'join' => 'round', 'dash' => 0, 'color' => array(0, 0, 0));
        $c1=array(215,215,215); //color gris de relleno definido con 3 valores es interpretado como RGB
        $c2=array(255, 255, 255); //color blanco de relleno definido con 3 valores es interpretado como RGB

        $s2=array('all'=>$est1);

        $x0 = PDF_MARGIN_LEFT - 5;
        // CAJA DE JUB. Y PENS. DE LA PROV. DE STA. FE    HECHO
        // ORGANISMO: ....                                HECHO
        // Fuente :       |  Tipo :
        //   ...               ...
        // Presentado :   |  Ctdad. aportantes:
        //   ...               ...




        // Marco general
        $this->RoundedRect ($x0, 60, 190, 200, 5, $round_corner = '1111',
            $style = '',
            $border_style = $est1,
            $fill_color = array()
        );

        // Título (Borde)
        $this->RoundedRect ($x0, 60, 190, 10, 5, $round_corner = '1001',
            $style = '',
            $est1,
            $c1
        );
        // Título (Relleno)
        $this->RoundedRect ($x0, 60, 190, 10, 5, $round_corner = '1001',
            $style = 'F',
            $est1,
            $c1
        );
        // Título (texto)
        $this->SetXY($x0, 62);
        $this->SetFont('helveticaB', '', 14);
        $this->Cell(180, 0, "CAJA DE JUBILACIONES Y PENSIONES DE LA PROVINCIA DE SANTA FE", 0, '', 'C');

        // SubTítulo
        $this->Rect($x0, 70, 30, 8, 'B', $s2, $c2);
        $this->Rect($x0+30, 70, 160, 8, '', $s2, $c2);
        
        // SubTítulo (texto1)
        $this->SetXY($x0, 71);
        $this->SetFont('helveticaB', '', 10);
        $this->Cell(28, 6, "Organismo:", 0, '', 'L', $fill=false, $valign='C');

        // SubTítulo (texto2)
        $this->SetXY($x0+31, 71);
        $this->SetFont('helvetica', '', 10);
        $this->Cell(155, 6, $this->parametros['codigo'].' - '.$this->parametros['nombre'], 0, '', 'L', $fill=false, $valign='C');

        // Fuente titulo (Borde)
        $this->RoundedRect ($x0 + 10, 83, 40, 5, 3, $round_corner = '1001',
            $style = '',
            $est1,
            $c1
        );
        // Fuente titulo (Relleno)
        $this->RoundedRect ($x0 + 10, 83, 40, 5, 3, $round_corner = '1001',
            $style = 'F',
            $est1,
            $c1
        );
        // Fuente título
        $this->SetXY($x0+10, 83);
        $this->SetFont('helveticaB', '', 10);
        $this->Cell(40, 0, 'Fuente', 0, '', 'C');

        // Fuente dato (Borde)
        $this->RoundedRect ($x0 + 10, 88, 40, 7, 3, $round_corner = '0110',
            $style = '',
            $est1,
            $c1
        );
        // Fuente dato
        $this->SetXY($x0+10, 88);
        $this->SetFont('helvetica', '', 10);
        $this->Cell(40, 7, $this->parametros['fuente'], 0, '', 'C<  ');

        // Tipo titulo (Borde)
        $this->RoundedRect ($x0 + 55, 83, 40, 5, 3, $round_corner = '1001',
            $style = '',
            $est1,
            $c1
        );
        // Tipo titulo (Relleno)
        $this->RoundedRect ($x0 + 55, 83, 40, 5, 3, $round_corner = '1001',
            $style = 'F',
            $est1,
            $c1
        );
        // Tipo
        $this->SetXY($x0+55, 83);
        $this->SetFont('helveticaB', '', 10);
        $this->Cell(40, 0, 'Tipo', 0, '', 'C');
        // Tipo dato (Borde)
        $this->RoundedRect ($x0 + 55, 88, 40, 7, 3, $round_corner = '0110',
            $style = '',
            $est1,
            $c1
        );
        // Tipo dato
        $this->SetXY($x0+55, 88);
        $this->SetFont('helvetica', '', 10);
        $this->Cell(40, 7, $this->parametros['tipo'], 0, '', 'C');





        // Presentado titulo (Borde)
        $this->RoundedRect ($x0 + 100, 83, 40, 5, 3, $round_corner = '1001',
            $style = '',
            $est1,
            $c1
        );
        // Presentado titulo (Relleno)
        $this->RoundedRect ($x0 + 100, 83, 40, 5, 3, $round_corner = '1001',
            $style = 'F',
            $est1,
            $c1
        );
        // Presentado
        $this->SetXY($x0+100, 83);
        $this->SetFont('helveticaB', '', 10);
        $this->Cell(40, 0, 'Presentado', 0, '', 'C');
        // Presentado dato (Borde)
        $this->RoundedRect ($x0 + 100, 88, 40, 7, 3, $round_corner = '0110',
            $style = '',
            $est1,
            $c1
        );
        // Presentado dato
        $this->SetXY($x0+100, 88);
        $this->SetFont('helvetica', '', 10);
        $this->Cell(40, 7, $this->parametros['presentado'], 0, '', 'C');






        // Ctdad aportantes titulo (Borde)
        $this->RoundedRect ($x0 + 145, 83, 40, 5, 3, $round_corner = '1001',
            $style = '',
            $est1,
            $c1
        );
        // Ctdad aportantes titulo (Relleno)
        $this->RoundedRect ($x0 + 145, 83, 40, 5, 3, $round_corner = '1001',
            $style = 'F',
            $est1,
            $c1
        );
        // Ctdad aportantes pesos
        $this->SetXY($x0+145, 83);
        $this->SetFont('helveticaB', '', 10);
        $this->Cell(40, 0, 'Ctdad. aportantes', 0, '', 'C');
        // Ctdad aportantes dato (Borde)
        $this->RoundedRect ($x0 + 145, 88, 40, 7, 3, $round_corner = '0110',
            $style = '',
            $est1,
            $c1
        );
        // Ctdad aportantes dato
        $this->SetXY($x0+145, 88);
        $this->SetFont('helvetica', '', 10);
        $this->Cell(40, 7, $this->parametros['ctdadAport'], 0, '', 'C');






        // Titulo: Concepto (Borde y relleno)
        $this->RoundedRect ($x0 + 10, 100, 25, 5, 3, $round_corner = '0001',
            $style = 'B',
            $est1,
            $c1
        );
        // Titulo: Nombre del Concepto (Borde y relleno)
        $this->Rect ($x0 + 35, 100, 90, 5,
            $style = 'B',
            $border_style = ''
        );
        
        // Titulo: Cantidad de Concepto (Borde y relleno)
        $this->Rect ($x0 + 125, 100, 30, 5,
            $style = 'B',
            $border_style = ''
        );
        
        // Titulo: Importe (Borde y relleno)
        $this->RoundedRect ($x0 + 145, 100, 40, 5, 3, $round_corner = '1000',
            $style = 'B',
            $est1,
            $c1
        );

        // Titulo: Concepto
        $this->SetXY($x0+3, 100);
        $this->SetFont('helveticaB', '', 10);
        $this->Cell(40, 0, 'Concepto', 0, '', 'C');

        // Titulo: Nombre de Concepto
        $this->SetXY($x0+25, 100);
        $this->SetFont('helveticaB', '', 10);
        $this->Cell(40, 0, 'Nombre', 0, '', 'C');
        
        // Titulo: Cantidad de Concepto
        $this->SetXY($x0+125, 100);
        $this->SetFont('helveticaB', '', 10);
        $this->Cell(20, 0, 'Cantidad', 0, '', 'C');
        
        // Titulo: Importe
        $this->SetXY($x0+145, 100);
        $this->SetFont('helveticaB', '', 10);
        $this->Cell(40, 0, 'Importe', 0, '', 'R');
        
        $y0 = 105;
        $dy = 5;
        foreach ($this->parametros['resumen'] as $itemResumen) {
            // Dato: Codigo (Borde)
            $this->Rect($x0 + 10, $y0, 25, $dy);
            // Dato: Nomre (Borde)
            $this->Rect($x0 + 35, $y0, 90, $dy);
            // Dato: Cantidad (Borde)
            $this->Rect($x0 + 125, $y0, 20, $dy);
            // Dato: Importe (Borde)
            $this->Rect($x0 + 145, $y0, 40, $dy);

            $codigo = $itemResumen['codigo'];
            $nombre = $itemResumen['nombre'];
            $cantidad = $itemResumen['cantidad'];
            $importe = number_format($itemResumen['importeTotal'], 2, ',', '.');
            
            // Codigo dato
            $this->SetXY($x0 + 10, $y0);
            $this->SetFont('helvetica', '', 10);
            $this->Cell(25,  $dy, $codigo, 0, '', 'C');
            
            // Nombre dato
            $this->SetXY($x0 + 35, $y0);
            $this->SetFont('helvetica', '', 10);
            $this->Cell(90,  $dy, $nombre, 0, '', 'L');
            
            // Cantidad dato
            $this->SetXY($x0 + 125, $y0);
            $this->SetFont('helvetica', '', 10);
            $this->Cell(20,  $dy, $cantidad, 0, '', 'C');
            
            // Signo $
            $this->SetXY($x0 + 145, $y0);
            $this->SetFont('helvetica', '', 10);
            $this->Cell(5,  $dy, '$', 0, '', 'C');
            
            // Importe dato
            $this->SetXY($x0 + 145, $y0);
            $this->SetFont('helvetica', '', 10);
            $this->Cell(40,  $dy, $importe, 0, '', 'R');
            $y0 = $y0 + $dy;
        }
        
        $this->RoundedRect($x0 + 10, $y0, 135,  $dy, 3, $round_corner = '0010', $style = 'B', $est1, $c1);
        $this->RoundedRect($x0 + 145, $y0, 40,  $dy, 3, $round_corner = '0100', $style = 'B', $est1, $c1);
        
        $this->SetXY($x0 + 10, $y0);
        $this->SetFont('helveticaB', '', 10);
        $this->Cell(110,  $dy, 'Cantidad de Conceptos Informados:', 0, '', 'L');
        
        $this->SetXY($x0 + 145, $y0);
        $this->SetFont('helvetica', '', 10);
        $this->Cell(40,  $dy, count($this->parametros['resumen']), 0, '', 'R');
    }

    public function Footer()
    {
        $this->Line(PDF_MARGIN_LEFT, 297 - $this->getFooterMargin(), 210 - PDF_MARGIN_RIGHT, 297 - $this->getFooterMargin());
        $this->Write(10, "CAJA DE JUBILACIONES DE LA PROVINCIA DE SANTA FE - SECTORIAL DE INFORMÁTICA", '', 0, 'L');

        $paddings = $this->getCellPaddings();
        $this->SetCellPadding(0 );
        $this->setX( - (PDF_MARGIN_RIGHT + 30 ) );
        $this->Cell(50 , 10, 'Página '.$this->getAliasNumPage().' de '.$this->getAliasNbPages(), 0, 0, "C" );
        $this->SetCellPadding( $paddings );
    }


}
