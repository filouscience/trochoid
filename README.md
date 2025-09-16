## Trochoid

**Hypotrchoids and Epitrochoids**: the locus of a point at a distance $d$ from a center of a circle
of radius $q$ rolling on a fixed circle of radius $p$ either on the inside (hypotrochoid) or the outside (epitrochoid).

Inspired by the [Spirograph](https://en.wikipedia.org/wiki/Spirograph), which allows only *curtate* trochoids $d<q$ for obvious reasons,
this project generalizes also to *prolate* trochoids $d>q$ and offers an animation using SVG and javascript.

The original version dates back to November/December 2013. For some reason, I found requesting the SVG from a PHP server and fetching it as a whole more feasible than creating it in-place with javascript.
The revolving circles are animated in SVG directly, the trail is drawn using javascript.
The end-point coordinates are obtained through transformation matrices.

| An example hypotrochoid $d:q:p = 4:3:8$ animation is shown here. | ![example hypotrochoid](/docs/trochoid_01.gif "Example Hypotrochoid") |
|-|-|

A GIF needs to be used here, for the javascript in SVG is disabled to prevent possible XSS (cross-site-scripting) attacks.

One can get the SVG directly by a PHP request to the server
`trochoid.php?p=8&q=3&d=4&s=-1`
or embed it in an interactive page, see below.

Part of the project was also a naive attempt at an approximation of the trochoid with cubic Bezier curves...

### embedding in an interactive page

- HTML:

an empty SVG container. The SVG is embedded in an `<object>` element in order to allow script execution.
```HTML
<div id="svg_container" style="float:left">
  <object id="M_SVG" width="500px" height="500px" type="image/svg+xml">
  </object>
</div>
```
and an input form:
```HTML
<form name="input_form" action="">
  p<input name="p_p" type="text" size="3" value="8" />
  q<input name="p_q" type="text" size="3" value="3" />
  d<input name="p_d" type="text" size="3" value="4" />
  <br />
  hypotrochoid<input name="p_s" type="radio" value="-1" checked="checked" />
  epitrochoid<input name="p_s" type="radio" value="1" />
</form>
<button onclick="get_svg()">display</button>
```

- javascript:

parse the input parameters and request the SVG from the PHP server:
```javascript
function get_svg() {
  const p = parseInt(document.input_form.p_p.value);
  const q = parseInt(document.input_form.p_q.value);
  const d = parseInt(document.input_form.p_d.value);
  const s = parseInt(document.input_form.p_s[0].checked ? document.input_form.p_s[0].value : document.input_form.p_s[1].value);
  if (isNaN(p) || p<1 || isNaN(q) || q<1 || isNaN(d) || d<0)
    return;

  const oldsvg = document.getElementById("M_SVG");
  document.getElementById("svg_container").removeChild(oldsvg);
  const newsvg = document.createElement("object");
  newsvg.setAttribute("id","M_SVG");
  newsvg.setAttribute("type","image/svg+xml");
  newsvg.setAttribute("width","500px");
  newsvg.setAttribute("height","500px");
  newsvg.setAttribute("data",("trochoid.php?p="+p+"&q="+q+"&d="+d+"&s="+s));
  document.getElementById("svg_container").appendChild(newsvg);
}
```
