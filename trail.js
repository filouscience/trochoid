  const delay1 = 7;
  const delay2 = 33;

  let duration,
      start_stamp,
      timer,
      opaque,
      drawing;

  function svg_onload()
  {
    duration = document.getElementById("self_svg").dataset.trailDuration;
    timer = setInterval(svg_dotrail, delay1);
    const now = new Date();
    start_stamp = now.getTime();
    opaque = 1;
    drawing = true;
  }

  function svg_dotrail()
  {
    // cx and cy are NOT being animated directly. The whole GROUP including the circle is animated.
    const mPoint = document.getElementById("M_Point");
    const b_x = mPoint.cx.baseVal.value;
    const b_y = mPoint.cy.baseVal.value;

    // instead, we use CTM "Current Transformation Matrix" (transformation relative to the fixed center)
    //  a  c  e
    //  b  d  f
    //  0  0  1
    const mCenter = document.getElementById("M_Center");
    const CTM = mCenter.getCTM().inverse().multiply( mPoint.getCTM() );    // CTM(center)^(-1) . CTM(point)

    const m_x = b_x*CTM.a + b_y*CTM.c + CTM.e;
    const m_y = b_x*CTM.b + b_y*CTM.d + CTM.f;

    const mVect = document.getElementById("R_Vect");
    mVect.setAttribute("x2", m_x);
    mVect.setAttribute("y2", m_y);

    if (!drawing)
        return;

    const mTrail = document.getElementById("M_Trail");
    const mPathData = mTrail.getAttribute("d")+" L"+(m_x).toFixed(2)+","+(m_y).toFixed(2);
    mTrail.setAttribute("d", mPathData );

    // break when the curve is completed
    const now = new Date();
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
  