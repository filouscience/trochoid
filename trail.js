  var delay1 = 7;
  var delay2 = 33;

  var duration,
      start_stamp,
      timer,
      opaque,
      drawing;

  var svgElement,
      mCenter,
      mPoint,
      mTrail;
  
  function svg_onload()
  {
    mCenter = document.getElementById("M_Center");
    //mRotor = document.getElementById("M_Rotor");
    mPoint = document.getElementById("M_Point");
    mTrail = document.getElementById("M_Trail");
    mVect = document.getElementById("R_Vect");

    duration = document.getElementById("self_svg").dataset.trailDuration;
    timer = setInterval(svg_dotrail, delay1);
    var d = new Date();
    start_stamp = d.getTime();
    opaque = 1;
    drawing = true;
  }

  function svg_dotrail()
  {
    // cx and cy are NOT being animated directly (though the whole group including the circle animated is)
    var b_x = mPoint.cx.baseVal.value;
    var b_y = mPoint.cy.baseVal.value;

    // instead, we use CTM "Current Transformation Matrix" (transformation relative to the center)
    //  a  c  e
    //  b  d  f
    //  0  0  1
    var CTM = mPoint.getTransformToElement(mCenter);

    var m_x = b_x*CTM.a + b_y*CTM.c + CTM.e;
    var m_y = b_x*CTM.b + b_y*CTM.d + CTM.f;

    mVect.setAttribute("x2", m_x);
    mVect.setAttribute("y2", m_y);

    if (!drawing)
        return;

    var new_segment = mTrail.createSVGPathSegLinetoAbs(m_x, m_y);
    mTrail.pathSegList.appendItem(new_segment);

    // break when the curve is completed
    var now = new Date();
    if(duration <= now.getTime() - start_stamp)
    {
      drawing = false;
      clearInterval(timer);
      timer = setInterval(svg_dotrail, delay2);
    }
  }

  function svg_onclick(evt)
  {
    if(opaque)
    {
      mRotor.setAttribute("opacity",0);
      opaque=0;
    }
    else
    {
      mRotor.setAttribute("opacity",1);
      opaque=1;
    }
  }
  