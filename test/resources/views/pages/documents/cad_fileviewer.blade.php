<?xml version="1.0" encoding="utf-8" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
	<link href="CV-JS/stylesheets/cvjs_54.css" media="screen" rel="stylesheet" type="text/css" />

	<script src="CV-JS/javascripts/jquery-2.1.0.js" type="text/javascript"></script>

	<script src="CV-JS/javascripts/jquery.qtip.min.js" type="text/javascript"></script>
	<link href="CV-JS/stylesheets/jquery.qtip.min.css" media="screen" rel="stylesheet" type="text/css" />

	<script src="CV-JS/javascripts/bootstrap.min.js" type="text/javascript"></script>
	<link href="CV-JS/stylesheets/bootstrap.min.css" media="screen" rel="stylesheet" type="text/css" />

	<script src="CV-JS/javascripts/snap.svg-min.js" type="text/javascript" ></script>
	<script src="CV-JS/javascripts/cadviewerjs_min.js" type="text/javascript" ></script>
	<script src="CV-JS/javascripts/cadviewerjs_setup_min.js" type="text/javascript" ></script>

	<script src="CV-JS/javascripts/cvjs_api_styles_2_0_22.js" type="text/javascript" ></script>

	<script type="text/javascript" src="CV-JS/javascripts/rgbcolor.js"></script>
	<script type="text/javascript" src="CV-JS/javascripts/StackBlur.js"></script>
	<script type="text/javascript" src="CV-JS/javascripts/canvg.js"></script>

	<link href="CV-JS/stylesheets/cvjs_modal_54.css" media="screen" rel="stylesheet" type="text/css" />
	<script src="CV-JS/javascripts/tms_cadviewerjs_modal_1_0_14.js" type="text/javascript" ></script>

	<script src="CV-JS/javascripts/jquery-ui.min.js" type="text/javascript"></script>
	<link href="CV-JS/stylesheets/jquery-ui.min.css" media="screen" rel="stylesheet" type="text/css" />

	<script type="text/javascript">

	var file = '<?php echo $file;?>';
	//alert(file);
	console.log(file);
	var allRrl       = "{{config('app.doc_url')}}";
	var FileNameUrl = allRrl+file;  // file for server to pick up
	//alert(FileNameUrl);
	//var FileNameUrl = "/home/cadviewer/creator/City_base_map.dwg";   // file for server to pick up, it can also be a local path on the server
	var FileNameUrlNoExtension = "City_base_map";


//	var FileNameUrl = "http://creator.vizquery.com/City_skyway_map.dwg";  // the file is somewhere different than the converter infrastructure



    // generic callback method, called when drawing or page is fully loaded
	function cvjs_OnLoadEnd(){
			// generic callback method, called when the drawing is loaded
			// here you fill in your stuff, call DB, set up arrays, etc..
			// this method MUST be retained as a dummy method! - if not implemeted -

			cvjs_resetZoomPan();

	}

	// generic callback method, tells which FM object has been clicked
	function cvjs_change_space(object){

	}

	function cvjs_ObjectSelected(rmid){
	 	// placeholder for method in tms_cadviewerjs_modal_1_0_14.js   - must be removed when in creation mode and using creation modal
	}

	$(document).ready(function()
		{


		if (!isSmartPhoneOrTablet)
			$('#gMenu').html("<img src=\"CV-JS/images/cvjsToolbar_6_z.png\" style=\"margin-left: 10px; margin-top: 10px;\" usemap=\"#cvjsToolbarMap_1\" border=\"0\" height=\"145\" width=\"62\" class=\"map\" hidefocus=\"true\">");
		else
			$('#gMenu').html("<img src=\"CV-JS/images/PanZoomWindowFullPages7t.png\" usemap=\"#PanZoomMap\" border=\"0\" height=\"363\" width=\"78\" class=\"map\" hidefocus=\"true\">");


		cvjs_setLicenseKeyPath("CV-JS/javascripts/");

		cvjs_setPanState(true);


		cvjs_debugMode(true);


//  Initialize CADViewer JS
		cvjs_InitCADViewerJS("floorPlan");

// load via REST

		// As an alternative, I can use:
		// cvjs_Init_ConversionServer("http://myserver/autoxchange_js_environment/","call-Api_JS_04_05.php", "", ""); // no username/password protected server

		// First we can check which mimimum rest API the current CADViewer JS version is initialized to:
		var mycontroller = cvjs_restApiController();

		// We also would like the recommended converter
		var converter = cvjs_restApiConverter();

		// We also would like to know the minimum recommended converter version
		var converterVersion = cvjs_restApiConverterVersion();

		//window.alert("This version of JS likes: "+mycontroller+"\nThe converter is: "+converter+"\nThe minimim recommended version is: "+converterVersion);


		// I need to set variables to point to my server where I have installed AutoXchange and the controlling scripts,
		cvjs_setRestApiControllerLocation("http://creator.vizquery.com/axtest/");
		cvjs_setRestApiController("call-Api_JS_04_05.php");


		// If I want to overwrite these values with settings for my own server where I have installed AutoXchange and the controlling scripts,
		//  I can control whem with the methods:
		//      cvjs_Init_ConversionServer(rest_api_url, rest_api_php, username, password);
		// or:  cvjs_setConverterCredentials(username, password);
		// or:  cvjs_setConverter(converter, version);



		// Now I want to set up the parameters for the conversion call, if I do not do anything, the conversion is set up to load FileNameUrl as a file (see RestAPI for other methods),
		// the resulting is located on the server as a stream. This means that CADViewer JS will read up the file and it is deleted on server after reading.
		// The conversion parameters are standard set-up from within AutoXchange:  -prec=2, -size=2800 -ml=0.4
		// When browsing through multiple layouts in a file, for each layout a new conversion will be triggered.

		// cvjs_LoadDrawing_Conversion("floorPlan", FileNameUrl, FileNameUrlNoExtension, "", "");




		// If I want to control the parameters controls in conversions I will call some of the following methods prior to conversion , see API documentation

		// I want to clear all pre-set autoxchange conversion parameters
		 cvjs_conversion_clearAXconversionParameters();
		// now I want to increase the size of the output drawing, this is useful for large drawings with much detail
		// cvjs_conversion_addAXconversionParameter("size", "4800");
		// now I want to make the minimum lines thinner, this is useful for large drawings with much detail
		// cvjs_conversion_addAXconversionParameter("ml", "0.4");
		// I also make a slightly lower precision to make the resulting file smaller, the original drawing is designed in a good resolution space
		cvjs_conversion_addAXconversionParameter("prec", "1");


		// For the server, I want to tell which path to the xrefs I want to use in this conversion, this is preset on the server to ./files/xref
		// cvjs_conversion_addAXconversionParameter("xpath", "/myserverlocation/files/xrefs2");



		// Now as an alternative, I want to set the content response to file instead of stream. This means that the server will keep a copy of the file (randomly named)
		// The pros is that I can quicker browse throught the multipages in the file
		// The cons is that loading of first page takes longer and that I need to clean up the server when I leave the page (or at the end of the day)
		// For this to work, I need to set the conversion parameter -basic, so that all pages in the set are converted initially

		// cvjs_conversion_setContentResponse("file");
		// cvjs_conversion_addAXconversionParameter("basic", "");



		// Now I will have the rest server pick up the dwg file at http://creator.vizquery.com/City_base_map.dwg, which is not password protected,
		// If the file is on the same server as the converter, I can pass over a local file and path: /home/cadviewer/creator/City_skyway_map.dwg
		// convert it, and send it up to CADViewer JS in this document for display

		cvjs_LoadDrawing_Conversion("floorPlan", FileNameUrl, FileNameUrlNoExtension, "", "");




// load Standard drawing
//		cvjs_LoadDrawing("floorPlan", FileNamePath, FileNameNoExtension);

// load SVG
//		cvjs_LoadDrawing_SVG("floorPlan", FileNamePath, "hq17.svg", "CV-JS/javascripts/");


	    cvjs_windowResize_position(false, "floorPlan" );

        });  // end ready()



        $(window).resize(function() {

			cvjs_windowResize_position(true, "floorPlan" );
        });


 	</script>

  </head>
  <body style="margin:0; background-color: white !important;" >

	<table id="tablerows">
	<tr>
	<td>
	<canvas id="dummy" width="10" height="10"></canvas>
	</td>
	<td>
	</td>
	<td>
	<canvas id="dummy" width="10" height="10"></canvas>
	</td>
	</tr>
	<tr>
	<td>
	<canvas id="dummy" width="10" height="10"></canvas>
	</td>
	<td>
	</td>
	<td>
	<canvas id="dummy" width="10" height="10"></canvas>
	</td>


	<tr>
	<td>
	<canvas id="dummy" width="10" height="10"></canvas>
	</td>
	</tr>

	<tr>
	<td>
		<svg id="floorPlan"  style="border:0px none;width:1000;height:400;">
		</svg>

		<map name="cvjsToolbarMap_1">
			<area shape="rect" alt="" title="Layers" coords="5,6,29,30" href="javascript:cvjs_layerList();"/>
			<area shape="rect" alt="" title="Print" coords="32,6,57,30" href="javascript:cvjs_printCanvasPaperSize();"/>
			<area shape="rect" alt="" title="Zoom In" coords="5,33,29,57" href="javascript:cvjs_zoomIn();"/>
			<area shape="rect" alt="" title="Zoom Out" coords="32,33,56,57" href="javascript:cvjs_zoomOut();"/>
			<area shape="rect" alt="" title="Zoom Extents" coords="6,60,29,85" href="javascript:cvjs_resetZoomPan();"/>
			<area shape="rect" alt="" title="Zoom Window" coords="33,61,57,85" href="javascript:cvjs_zoomWindow();"/>
			<area shape="rect" alt="" title="Load Previous Page" coords="5,87,29,111" href="javascript:cvjs_previousPage();"/>
			<area shape="rect" alt="" title="Load Next Page" coords="32,88,57,111" href="javascript:cvjs_nextPage();"/>
			<area shape="rect" alt="" title="Load First Page" coords="5,114,29,139" href="javascript:cvjs_firstPage();"/>
			<area shape="rect" alt="" title="Load Last Page" coords="32,114,56,138" href="javascript:cvjs_lastPage();"/>
		</map>



		<map name="PanZoomMap" >
			<area shape="rect" alt="" title="Zoom Extents" coords="16,6,69,58" href="javascript:cvjs_resetZoomPan();"/>
			<area shape="rect" alt="" title="Zoom In" coords="16,66,69,115" href="javascript:cvjs_zoomIn();"/>
			<area shape="rect" alt="" title="Zoom Out" coords="16,116,69,161" href="javascript:cvjs_zoomOut();"/>
			<area shape="rect" alt="" title="Zoom Window" coords="16,162,69,210" href="javascript:cvjs_zoomWindow();"/>
			<area shape="rect" alt="" title="Load Last Page" coords="16,220,69,255" href="javascript:cvjs_lastPage();"/>
			<area shape="rect" alt="" title="Load Next Page" coords="16,256,69,289" href="javascript:cvjs_nextPage();"/>
			<area shape="rect" alt="" title="Load Previous Page" coords="16,290,69,324" href="javascript:cvjs_previousPage();"/>
			<area shape="rect" alt="" title="Load First Page" coords="16,325,69,360" href="javascript:cvjs_firstPage();"/>
		</map>



	<!-- <div id="gMenu"></div> -->
	<div id="tip" ></div>
	<div id="wait_looper"></div>
	</td>
	</tr>
	</table>

	<table>
	<tr>
	<td>
	<canvas id="floorPlanCanvasObject" width="10" height="10"></canvas>
	</td>
	</tr>
	</table>




</body>
<style type="text/css">
	text{
        visibility: hidden !important;
    }

</style>
</html>
