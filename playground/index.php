<!DOCTYPE html>
<?php 

    $link = mysqli_connect('localhost', 'root', '', 'makerjs');
    $sql="SET NAMES UTF8";
    $link->query($sql);

    function get_last_no ( $primary_key, $table)
    {
        global $link;
        $sql_lastno  = "SELECT ".$primary_key;
        $sql_lastno .= " FROM ".$table;
        $sql_lastno .= " ORDER BY ".$primary_key;
        $sql_lastno .= " DESC LIMIT 1";
        $result_lastno = $link->query($sql_lastno); 
        $number = 1;
        while($row = mysqli_fetch_row($result_lastno))
        {
            $number = $row[0]+1;
            break;
        } 
        
        mysqli_free_result($result_lastno);
        return $number;
    }


    if(isset($_POST["LINE_CONTENT"])){
        $sn = get_last_no("SN", "code");
        $sql="INSERT INTO code(
                     SN ,
                     CODE )
                     VALUES(
                     '".$sn."' ,
                     '".$_POST["LINE_CONTENT"]."' )
             ";

        $result=$link->query($sql);
    }

    if(isset($_POST["CIRCLE_CONTENT"])){
        $sn = get_last_no("SN", "code");
        $sql="INSERT INTO code(
                     SN , 
                     CODE )
                     VALUES(
                     '".$sn."' ,
                     '".$_POST["CIRCLE_CONTENT"]."' )
             ";

        $result=$link->query($sql);
    }
    
?>
<?php
    $sql="SELECT CODE
            FROM code
         ";
    $result=$link->query($sql);
    $row=mysqli_fetch_all($result, MYSQLI_ASSOC);


    $sn = get_last_no("SN", "code");

?>

<html>
<head>
    <title>Maker.js Playground</title>
    <meta charset="utf-8" />
    <!-- <meta name="viewport" content="user-scalable=yes, width=device-width, initial-scale=1, maximum-scale=1"> -->

    <!--

    *****************************************************************************
    Copyright (c) Microsoft Corporation. All rights reserved.
    Licensed under the Apache License, Version 2.0 (the "License"); you may not use
    this file except in compliance with the License. You may obtain a copy of the
    License at http://www.apache.org/licenses/LICENSE-2.0

    THIS CODE IS PROVIDED ON AN *AS IS* BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY
    KIND, EITHER EXPRESS OR IMPLIED, INCLUDING WITHOUT LIMITATION ANY IMPLIED
    WARRANTIES OR CONDITIONS OF TITLE, FITNESS FOR A PARTICULAR PURPOSE,
    MERCHANTABLITY OR NON-INFRINGEMENT.

    See the Apache Version 2.0 License for specific language governing permissions
    and limitations under the License.
    *****************************************************************************

    https://github.com/Microsoft/maker.js

    -->
    <link href="../bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- MetisMenu CSS -->
    <link href="../bower_components/metisMenu/dist/metisMenu.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="../dist/css/sb-admin-2.css" rel="stylesheet">

    <!-- Custom Fonts -->
    <link href="../bower_components/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">

    <script src="../bower_components/jquery/dist/jquery.min.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="../bower_components/bootstrap/dist/js/bootstrap.min.js"></script>

    <!-- Metis Menu Plugin JavaScript -->
    <script src="../bower_components/metisMenu/dist/metisMenu.min.js"></script>

    <!-- Custom Theme JavaScript -->
    <script src="../dist/js/sb-admin-2.js"></script>

    <link href="../external/codemirror/lib/codemirror.css" rel="stylesheet" />
    <link href="../external/codemirror/theme/twilight.css" rel="stylesheet" />
    <link href="playground.css" rel="stylesheet" />
    <script src="../external/pep/pep.js"></script>
    <script src="../external/marked/marked.js"></script>
    <script src="../external/codemirror/lib/codemirror.js"></script>
    <script src="../external/codemirror/mode/javascript/javascript.js"></script>
    <script src="../external/bezier-js/bezier.js"></script>
    <script src="../external/opentype/opentype.js"></script>

    <script src="../target/js/browser.maker.js"></script>
    <script>
        var makerjs = require('makerjs');
    </script>

    <script src="iexport.js"></script>
    <script src="pointer.js"></script>
    <script src="playground.js"></script>
    <script>
        //config
        MakerJsPlayground.relativePath = '../examples/';
    </script>

</head>

<body class="no-notes collapse-annotation">
    <a name="top"></a>

    <main class="row">
        <header class="logo row">

<form method="POST">
            <div id="rendering-options-top">
                <button onclick="MakerJsPlayground.toggleClassAndResize('collapse-rendering-options');">自訂 <span class="icon dropup">&#x25B4;</span><span class="icon dropdown">&#x25BE;</span></button>
            </div>

            <div class="row cad_tools">
                <div class="col-md-1"><input type="button" class="btn btn-md btn-warning" id="btn_line" value="直線" /></div>
                <div class="col-md-1"><input type="button" class="btn btn-md btn-warning" id="btn_circle" value="圓形" /></div>
                <div class="col-md-1"><input type="button" class="btn btn-md btn-warning" id="btn_arc" value="弧形" /></div>
                <div class="col-md-1"><input type="button" class="btn btn-md btn-warning" id="btn_rec" value="矩形" /></div>
            </div>
            遊標位置
            <div class="mouse_position" id="mouse_postition"></div>
            <div class="tool_detail">
                <div class="line">
                    <div class="row "> 原點 X<input id="start_x" /> Y<input id="start_y" /></div>
                    <div class="row "> 終點 X<input id="end_x" /> Y<input id="end_y" /></div>
                    <input type="hidden" id="LINE_CONTENT" name="LINE_CONTENT" disabled/>
                </div>
                <div class="circle">
                    <div class="row "> 原點 X<input id="c_start_x" /> Y<input id="c_start_y" /></div>
                    <div class="row "> 半徑 R<input id="radius" /></div>
                    <input type="hidden" id="CIRCLE_CONTENT" name="CIRCLE_CONTENT" disabled/>
                </div>
                <div class="arc">
                    <div class="row "> 原點 X<input id="arc_start_x" /> Y<input id="arc_start_y" /></div>
                    <div class="row "> 半徑 R<input id="arc_radius" /></div>
                    <div class="row "> 起始角<input id="arc_r_start" /> 結束角<input id="arc_r_end" /></div>
                    <input type="hidden" id="ARC_CONTENT" name="ARC_CONTENT" disabled/>
                </div
                
            </div>
            <input type="submit" class="btn btn-danger" id="btn_addline" value="增" onclick="return add_line();"/>    
</form> 
        </header>

        <section class="row" id="blueprint-canvas" style="width:device-width">
            <div id="view-params">
                <div id="view" touch-action="none" class="noselect">
                    <div id="view-svg-container"></div>
                    <svg id="pointers" xmlns="http://www.w3.org/2000/svg"></svg>
                    <div id="touch-shield"></div>
                </div>
                <div id="rendering-options-menu" class="noselect">
                    <div id="params"></div>
                    <div class="view-controls">
                        <div>

                        </div>
                        <div><label><input id="check-fit-on-screen" type="checkbox" checked onclick="if (this.checked) { MakerJsPlayground.fitOnScreen(); } else { MakerJsPlayground.fitNatural(); } MakerJsPlayground.render();" /> 符合視窗 <span id="zoom-display"></span></label></div>
                        <div><label><input id="check-show-origin" type="checkbox" checked onclick="MakerJsPlayground.toggleClass('collapse-origin')" /> 顯示原始尺寸</label></div>
                        <div><label><input id="check-annotate" type="checkbox" onclick="MakerJsPlayground.toggleClass('collapse-annotation')" /> 顯示路徑名稱</label></div>
                        <div id="notes-toggle"><label><input id="check-notes" type="checkbox" checked onclick="MakerJsPlayground.toggleClass('collapse-notes')" /> show notes</label></div>
                    </div>
                </div>
            </div>
            <div id="notes"></div>
        </section>



        <section class="editor row" id="editor">
            <div>
                <div class="code-header">
                    <span>JavaScript code editor</span>
                    <button class="run" onclick="MakerJsPlayground.runCodeFromEditor()">&nbsp;&#x25BA; Run</button>
                    <span class="status"></span>
                </div>

                <a name="code"></a>

<pre id="init-javascript-code">
var makerjs = require('makerjs')
var model = {
    paths: {
<?php 
    
    foreach ($row as $key => $value) {
        # code...
      echo $row[$key]["CODE"];
    }

?>

    }

};
if ( !!Object.keys(model.paths).length ) {
    var svg = makerjs.exporter.toSVG(model);
    document.write(svg);
}

</pre>
            </div>
        </section>
        <!-- section class="editor -->




        <footer class="row">

        </footer>

    </main>
    <script>
        $("#btn_line").click(function(){
            $(".line").show();
            $(".circle").hide();
            $(".arc").hide();
            $(".rec").hide();
            $("#btn_addline").show();
            $("#btn_addline").attr("onclick", "return add_line();");
        });
        $("#btn_circle").click(function(){
            $(".line").hide();
            $(".circle").show();
            $(".arc").hide();
            $(".rec").hide();
            $("#btn_addline").show();
            $("#btn_addline").attr("onclick", "return add_circle();");
        });
        $("#btn_arc").click(function(){
            $(".line").hide();
            $(".circle").hide();
            $(".arc").show();
            $(".rec").hide();
            $("#btn_addline").show();
        });
        $("#btn_rec").click(function(){
            $(".line").hide();
            $(".circle").hide();
            $(".arc").hide();
            $(".rec").show();
            $("#btn_addline").show();
        });
    </script>
    <script>
        var growbal={};
        var i = <?php echo $sn;?>;
        console.log(i);
        // $("#btn_addline").click(function() {
        function add_line(){
            
            $("#LINE_CONTENT").val(
                '"L'+i+'": new makerjs.paths.Line(['+  $("#start_x").val() +','+ $("#start_y").val()+'], ['+
                $("#end_x").val()+',' + $("#end_y").val()+'] ),'
            );
            $("#LINE_CONTENT").removeAttr("disabled");
            return true;
        };
        
        function add_circle(){
            
            $("#CIRCLE_CONTENT").val(
                '"C'+i+'": new makerjs.paths.Circle(['+  $("#c_start_x").val() +','+ $("#c_start_y").val()+'], '+
                $("#radius").val()+' ),'
            );
            $("#CIRCLE_CONTENT").removeAttr("disabled");

            return true;
        };
        function add_arc(){
            
            $("#ARC_CONTENT").val(
                '"A'+i+'": new makerjs.paths.Arc(['+  $("#arc_start_x").val() +','+ $("#arc_start_y").val()+'], '+
                $("#arc_radius").val()+ $("#arc_r_start").val()+','+ $("#arc_r_end").val() +' ),'
            );
            $("#ARC_CONTENT").removeAttr("disabled");
            // var arc = new makerjs.paths.Arc([0, 0], 25, 0, 90);
            return true;
        };
        function add_rec(){
            
            $("#CIRCLE_CONTENT").val(
                '"C'+i+'": new makerjs.paths.Circle(['+  $("#c_start_x").val() +','+ $("#c_start_y").val()+'], '+
                $("#radius").val()+' ),'
            );
            $("#CIRCLE_CONTENT").removeAttr("disabled");

            return true;
        };
        
        
    </script>

    <script type="text/javascript">
        $(function() {
            // $("#editor").hide();
            // $(".tool_detail").hide();
            $(".line").hide();
            $(".circle").hide();
            $(".arc").hide();
            $(".rec").hide();
            $("#btn_addline").hide();
            $("body").removeClass("side-by-side");

            // $("#rendering-options-menu").hide();
        });
        $("iframe").attr("id", "hi");
    </script>
    <script type="text/javascript">
        // var add_from = 6;

        // function add_line() {
        //     console.log("addalin");
        //     $(".CodeMirror-code div").eq(add_from).html(

        //         '<div class="CodeMirror-gutter-wrapper" style="left: -30px;">' +
        //         '<div class="CodeMirror-linenumber CodeMirror-gutter-elt" style="left: 0px; width: 21px;">' + add_from + '</div>' +
        //         '</div>' +
        //         '<pre class=" CodeMirror-line ">' +
        //         '<span style="padding-right: 0.1px;">' +
        //         '<span class="cm-string cm-property">"line' + add_from + '"</span>: ' +
        //         '<span class="cm-keyword">new</span> ' +
        //         '<span class="cm-variable">makerjs</span>.' +
        //         '<span class="cm-property">paths</span>.' +
        //         '<span class="cm-property">Line</span>([<span class="cm-number">' + $("#start_x").val() + '</span>, <span class="cm-number">' + $("#start_y").val() + '</span>], [<span class="cm-number">' + $("#end_x").val() + '</span>, <span class="cm-number">' + $("#end_y").val() + '</span>]),</span>' +
        //         '</pre>'
        //     );
        // $(".CodeMirror-code div").eq(add_from).after(
        //   '<div id="hi" style="position: relative;">'+
        //     '<div class="CodeMirror-gutter-wrapper" style="left: -30px;">'+
        //       '<div class="CodeMirror-linenumber CodeMirror-gutter-elt" style="left: 0px; width: 21px;">'+add_from+'</div>'+
        //     '</div>'+
        //     '<pre class=" CodeMirror-line ">'+
        //       '<span style="padding-right: 0.1px;">'+
        //         '<span class="cm-string cm-property">"l1"</span>: '+
        //         '<span class="cm-keyword">new</span> '+
        //         '<span class="cm-variable">makerjs</span>.'+
        //         '<span class="cm-property">paths</span>.'+
        //         '<span class="cm-property">Line</span>([<span class="cm-number">'+$("#start_x").val()+'</span>, <span class="cm-number">'+$("#start_y").val()+'</span>], [<span class="cm-number">'+$("#end_x").val()+'</span>, <span class="cm-number">'$("#end_x").val()+'</span>]),</span>'+
        //     '</pre>'+
        //   '</div>'
        // );
        // add_from++;
        // }
    </script>
</body>

</html>