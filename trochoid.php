<?php
header('Content-type: image/svg+xml');
echo('<?xml version="1.0" encoding="UTF-8" standalone="yes"?>');
?>

<?php
/* get input ***************************** */
/* *************************************** */

/* shape ---- 1: epitrochoid / -1: hypotrochoid */
$shape = -1;
if(array_key_exists("s",$_GET))
  $shape = $_GET['s'];

/* radius ratios */
$i_p = 8;
if(array_key_exists("p",$_GET))
  $i_p = $_GET['p'];

$i_q = 3;
if(array_key_exists("q",$_GET))
  $i_q = $_GET['q'];

$i_d = 4;
if(array_key_exists("d",$_GET))
  $i_d = $_GET['d'];

if($i_p == 0 || $i_q == 0)
  die();
/* *************************************** */
/* type  ---- 1: cycloid / -1: trochoid */
$type = ($i_q == $i_d ? 1 : -1);

$GCD = 1;
if($i_p < 1 || $i_q  < 1)
  die();
if($i_p == $i_q)
  $GCD = $i_p;
else
/* Euclidean Algorithm ******************* */
/*  GCD ( u , v ) == GCD ( v , u - q * v ) */
/*  .....                                  */
/*  GCD ( t , 0 ) == t                     */
/* *************************************** */
{
$u = ($i_p > $i_q ? $i_p : $i_q);
$v = ($i_p < $i_q ? $i_p : $i_q);
$s = 0;
  while($v > 0)
  {
    $s = $u % $v;
    $u = $v;
    $v = $s;
  }
$GCD = $u;
}
/* *************************************** */
$p = $i_p / $GCD;
$q = $i_q / $GCD;
$d = $i_d / $GCD;

/* get the final values ****************** */
/* not to exceed image size ************** */
/* *************************************** */
$size = 450;
$period = 3*$p/$q;

$dist;
$R;
$r;

if($type == 1) {
  if($shape == 1) // epicycloid
  {
    $R = ($size*$p)/($p+2*$q);
    $r = ($size*$q)/($p+2*$q);
  }
  else // hypocycloid
  {
    if($q > $p)
    {
      $R = ($size*$p)/(-$p+2*$q);
      $r = ($size*$q)/(-$p+2*$q);
    }
    else
    {
      $R = $size;
      $r = ($size*$q)/($p);
    }
  }
  $dist = $r;
} else {
  if($shape == 1) // epitrochoid
  {
    $R = ($size*$p)/($p+$q+$d);
    $r = ($size*$q)/($p+$q+$d);
    $dist = ($size*$d)/($p+$q+$d);
  }
  else // hypotrochoid
  {
    if($q == $p)
    {
      $R = $size;
      $r = $size;
      $dist = $size;
    }
    if($q > $d)
    {
      $R = ($size*$p)/($p+$q-$d);
      $r = ($size*$q)/($p+$q-$d);
      $dist = ($size*$d)/($p+$q-$d);
    }
    else if($q > $p)
    {
      $R = ($size*$p)/(-$p+$q+$d);
      $r = ($size*$q)/(-$p+$q+$d);
      $dist = ($size*$d)/(-$p+$q+$d);
    }
    else
    {
      $R = ($size*$p)/(+$p-$q+$d);
      $r = ($size*$q)/(+$p-$q+$d);
      $dist = ($size*$d)/(+$p-$q+$d);
    }
  }
}
/* *************************************** */

/* prepare the SVG element */
echo('
<!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.1//EN" "http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd">

<svg id="self_svg"
    width="100%"
    height="100%"
    viewBox="0 0 1000 1000"
    xmlns="http://www.w3.org/2000/svg"
    version="1.1"
    onload = "svg_onload();"
    onclick = "svg_onclick();"
    data-trail-duration = '.($period * $q * 1000).'
    >
  <title>trochoid_svg</title>
  
  <link xmlns="http://www.w3.org/1999/xhtml" rel="stylesheet" href="style.css"/>
  <script type="text/javascript" href="trail.js" />

  <rect class="Border" x="2" y="2" width="996" height="996" />
  <line class="Axis" x1="500" y1="0" x2="500" y2="1000" />
  <line class="Axis" x1="0" y1="500" x2="1000" y2="500" />
');
?>


<?php
/* ################## -------------------------------------------------------------------------------------------------- ################## */
/* ################## this chunk creates a roughly approximate trajectory using cubic Bezier curves - it is far not good ################## */
/* ################## -------------------------------------------------------------------------------------------------- ################## */

$use_bezier = true;
/* accuracy of control points distance */
/* -- I cant really explain this :) -- */
$coef_o; /* adjust distance of control point of cubic Bezier curve (odd phase)  MAGIC HERE */
$coef_e; /* adjust distance of control point of cubic Bezier curve (even phase) MAGIC HERE */

if($i_d==0) /* circle */
{
    /* four-point approximation of a circle */
    $coef_o = (sqrt(2)-1)*($R+$shape*$r)*4/3;
    $coef_e = (sqrt(2)-1)*($R+$shape*$r)*4/3;
}
else if($shape==1) /* epitrochoids */
{
  if($p > 2*$q)
  {
    if($type == 1) /* epicycloid */
    {
      /* TODO: formulae */
      $coef_o = (sqrt(2)-1)*(($r+$dist)*M_PI*sqrt(2)*2/M_PI)*4/3;
      $coef_e = -(sqrt(2)-1)*(($r+$dist)*sqrt(2)*2/M_PI)*4/3; /* old: $coef_e=0 */
    }
    else /* epitrochoid */
    {
      /* TODO: formulae */
      $coef_o = ($r+$dist)*sqrt(($R+$dist)/($r));
      $coef_e = ($r-$dist)*($R)/($r);
    }
  }
  else
  {
    /* you'd better not */
    $use_bezier = false;
    //$coef_o = ($r+$dist)*($R+$dist+$r)/($R+$dist);
    //$coef_e = ($r-$dist)*($R+$dist+$r)/($r+$dist);
  }
}
else /* hypotrochoids */
{
  if($p>$q)
  {
    if($p == 2*$q) /* ellipse */
    {
      /* affine transform of the circle approximation above */
    $coef_o = (sqrt(2)-1)*($r+$dist)*4/3;
    $coef_e = (sqrt(2)-1)*($r-$dist)*4/3;
    }
    else if($type == 1) /* hypocycloid */
    {
      /* TODO: formulae */
      $coef_o = ($r+$dist)*(1-($r/$R));
      $coef_e = ($r+$dist)*(1-($r/$R)); /* old: $coef_e=0 */
    }
    else /* hypotrochoid */
    {
      /* TODO: formulae */
      $coef_o = ($r+$dist)*(($R+$dist)/($r+$dist)-1);
      $coef_e = ($r-$dist)*(($R+$dist)/($r+$dist)-1);
    }
  }
  else
  {
    /* you'd better not */
    $use_bezier = false;
    //$coef_o = ($r+$dist)*($R+$dist)/($r+$dist)*(-1);
    //$coef_e = ($r-$dist)*($R+$dist)/($r+$dist)*(-1);
  }
}
/* ----------------------------------- */

if($use_bezier)
{
  $step = M_PI*$q/$p;
  $total = 2*$p;

  if($i_d==0) /* circle */
  {
    $step = M_PI/2;
    $total = 4;
  }

  /* starting point */
  $x = 500+$R+($r-$dist)*$shape;
  $y = 500;

  echo('<path id="M_Bezier" class="Bezier" d="M'.$x.',500 ');
  /* first control point 1: */
  if($type == 1) /* cycloid */
  {
    $x1 = $x - $coef_e;
    $y1 = $y;
  }
  else /* trochoid */
  {
    $x1 = $x;
    $y1 = $y - $coef_e;
  }

  $command = '';

  for($i = 1; $i <= $total; $i++)
  {
    $coef;
    $delta = 0;
    if($i&1) {
      $coef = $coef_o; /* i is (i=2k-1): odd phase */
    } else {
      $coef = $coef_e; /* i is (i=2k): even phase */
      if($type == 1) /* cycloid */
        $delta = M_PI/2;  /* (the derivative of radius as a function of angle is infinite in the cusps) */
    }

    if($type == 1 && $i != 1)
    {
      /* control point 1: */
      $x1 = $x2;
      $y1 = $y2;
    }

    /* target point: */
    $x = 500 + ($R+($r*$shape))*cos($i*$step*(-1)) - $shape*$dist*cos((($R+($r*$shape))/$r)*$i*$step*$shape);
    $y = 500 + ($R+($r*$shape))*sin($i*$step*(-1)) + $shape*$dist*sin((($R+($r*$shape))/$r)*$i*$step*$shape);

    /* control point 2: */
    $x2 = $x + (-1)*sin($i*$step*(-1)+$delta) * $coef;
    $y2 = $y +  (1)*cos($i*$step*(-1)+$delta) * $coef;

    if($i == 1)
      $command = 'C'.$x1.','.$y1.' '.$x2.','.$y2.' '.$x.','.$y;
    else if($type == 1 && ($i&1) == 1)
      $command = 'C'.$x1.','.$y1.' '.$x2.','.$y2.' '.$x.','.$y;
    else
      $command = 'S'.$x2.','.$y2.' '.$x.','.$y;

    echo(' '.$command);
  }
  echo('" />');
}
/* ################## -------------------------------------------------------------------------------------------------- ################## */
?>


<?php 
/* create circles and let them rotate based on trochoid definition */ 
echo('
  <circle id="M_Center" class="Circle" cx="500" cy="500" r="0" />
  <line id="R_Vect" class="Vector2" x1="500" y1="500" x2="'.(500+$R+($r-$dist)*$shape).'" y2="500" />
  <g id="M_Rotor" opacity="1">
    <circle class="Circle" cx="500" cy="500" r="'.$R.'" />
    <g>
      <circle class="Point" cx="'.(500+$R+$r*$shape).'" cy="500" r="4" />
      <line class="Vector" x1="500" y1="500" x2="'.(500+$R+$r*$shape).'" y2="500" />
      <circle class="Circle" cx="'.(500+$R+$r*$shape).'" cy="500" r="'.$r.'" />
      <g>
        <circle id="M_Point" class="Point" cx="'.(500+$R+($r-$dist)*$shape).'" cy="500" r="7" />
        <line class="Vector" x1="'.(500+$R+$r*$shape).'" y1="500" x2="'.(500+$R+($r-$dist)*$shape).'" y2="500" />
        <animateTransform dur="'.$period*($r/$R).'s" type="rotate" from="0,'.(500+$R+$r*$shape).',500" to="'.(-360*$shape).','.(500+$R+$r*$shape).',500" repeatCount="indefinite" attributeName="transform"/>
      </g>
      <animateTransform dur="'.$period.'s" type="rotate" from="0,500,500" to="-360,500,500" repeatCount="indefinite" attributeName="transform"/>
    </g>
  </g>
  <path id="M_Trail" class="Trail" d="M'.(500+$R+($r-$dist)*$shape).',500" />
');
?>

<?php echo('</svg>'); ?>
