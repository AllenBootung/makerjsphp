<!DOCTYPE html>
<?php 

    $link = mysqli_connect('localhost', 'root', '', 'makerjs');
    $sql="SET NAMES UTF8";
    $link->query($sql);
    if (isset($_GET["TIME_NO"])) {
        $time_no = sql_injection($_GET["TIME_NO"]);
    } else {
        $time_no = date("Y-m-d h:i:s");
    }
    
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

    function sql_injection($str) 
    {
        global $link;
        $str = mysqli_real_escape_string($link,$str);
        $str = htmlspecialchars($str);
        return $str;
    }


    if(isset($_POST["CODE"])) {
        $sn = get_last_no("SN", "code");
        $sql="INSERT INTO code(
                     SN ,
                     CODE ,
                     ELEMENT_NAME ,
                     TIME_NO )
                     VALUES(
                     '".$sn."' ,
                     '".sql_injection($_POST["CODE"])."' ,
                     '".sql_injection($_POST["ELEMENT_NAME"])."' ,
                     '".$time_no."' )
             ";
        $result=$link->query($sql);
    }

    if (isset($_POST["DEL"])) {
        $sql = "DELETE FROM code
                 WHERE SN = '".sql_injection($_POST["SN"])."'
                   AND TIME_NO = '".$time_no."'
               ";
        $result=$link->query($sql);
    }

    
?>
<?php //印出
    
    $sql="SELECT SN, CODE, ELEMENT_NAME
            FROM code
           WHERE TIME_NO = '".$time_no."'
           ORDER BY SN ASC 
         ";
    $result = $link->query($sql);
    $row = mysqli_fetch_all($result, MYSQLI_ASSOC);
    echo '<script>var php_element = '. json_encode($row) . ';</script>' ;
    
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
<style type="text/css">
    .not_fill {
      animation: blink .5s step-end infinite alternate;
      border-style: solid;
    }
    @keyframes blink {
      50% {
          border-color: red;
          border-style: solid;
      }
    }
</style>
<body class="no-notes collapse-annotation">
    <a name="top"></a>

    <main class="row">
        <header class="logo row">

            <form method="POST" action="index.php?TIME_NO=<?php echo $time_no;?>">
                <input type="hidden" name="TIME_NO" value="<?php echo $time_no;?>">
                <div id="rendering-options-top">
                    <button onclick="MakerJsPlayground.toggleClassAndResize('collapse-rendering-options');">自訂 <span class="icon dropup">&#x25B4;</span><span class="icon dropdown">&#x25BE;</span></button>
                </div>

                <div class="row cad_tools">
                    <div class="col-md-1"><input type="button" class="btn btn-md btn-warning" id="btn_line" value="直線" /></div>
                    <div class="col-md-1"><input type="button" class="btn btn-md btn-warning" id="btn_circle" value="圓形" /></div>
                    <div class="col-md-1"><input type="button" class="btn btn-md btn-warning" id="btn_arc" value="弧形" /></div>
                    <div class="col-md-1"><input type="button" class="btn btn-md btn-warning" id="btn_del" value="刪除" /></div>
                </div>
                <!-- 遊標位置
                <div class="mouse_position" id="mouse_postition"></div> -->
                <div id="tool_detail">
                    
                </div>
                
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

        <section class="row" id="history" style="width: device-width">

        </section>
        <section id="download">

            <div id="download-select">
                <h2>請選擇要匯出的格式(尺寸將為mm)</h2>

                <button href="#" onclick="MakerJsPlayground.downloadClick(this, MakerJsPlaygroundExport.ExportFormat.Dxf);">.dxf</button>
                <button href="#" onclick="MakerJsPlayground.downloadClick(this, MakerJsPlaygroundExport.ExportFormat.Svg);">.svg</button>
                <button href="#" onclick="MakerJsPlayground.downloadClick(this, MakerJsPlaygroundExport.ExportFormat.Json);">json</button>
                <button href="#" onclick="MakerJsPlayground.downloadClick(this, MakerJsPlaygroundExport.ExportFormat.Pdf);">.pdf</button>
            </div>

            <div id="download-generating">
                <h2>計算中...</h2>

                <button onclick="MakerJsPlayground.cancelExport()">取消</button>
                <div id="download-progress"></div>
            </div>

            <div id="download-ready">
                <h2>計算完畢，點此下載↓</h2>

                <span id="download-link-container"></span>
                <span>
                    <button class="close" onclick="MakerJsPlayground.toggleClass('download-ready')">close</button>
                </span>
                <div id="download-preview-container">
                    <textarea id="download-preview" rows="8" readonly></textarea>
                </div>
                
            </div>

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
var makerjs = require('makerjs');
var model = {
    paths: {

<?php 
    
    foreach ($row as $key => $value) {
      echo "      ". $row[$key]["CODE"]. PHP_EOL;
    } //foreach ($row as $key => $value)

?>

    }

};

if ( !!Object.keys(model.paths).length ) {
    var svg = makerjs.exporter.toSVG(model);
    document.write(svg);
}

</pre>
            </div>
        </section><!-- section  class="editor row" id="editor"-->




        <footer class="row">

        </footer>

    </main>


    <script>//新增元素
        var growbal = growbal || {};
        growbal.SN = <?php echo $sn;?>;

        function add_line()
        {
            
            $("#CODE").val(
                '"L'+ growbal.SN +'": new makerjs.paths.Line(['+  $("#start_x").val() +','+ $("#start_y").val()+'], ['+
                $("#end_x").val()+',' + $("#end_y").val()+'] ),'
            );
            
            
            return all_check();
        };
        
        function add_circle()
        {
            
            $("#CODE").val(
                '"C'+ growbal.SN +'": new makerjs.paths.Circle(['+  $("#c_start_x").val() +','+ $("#c_start_y").val()+'], '+
                $("#radius").val()+' ),'
            );
            
           
            return all_check();
        };

        function add_arc()
        {
            // var arc = new makerjs.paths.Arc([0, 0], 25, 0, 90);
            $("#CODE").val(
                '"A'+ growbal.SN +'": new makerjs.paths.Arc(['+  $("#arc_start_x").val() +','+ $("#arc_start_y").val()+'], '+
                $("#arc_radius").val()+ ',' + $("#arc_r_start").val()+','+ $("#arc_r_end").val() +' ),'
            );
            
            
            return all_check();
        };
        
   
        
    </script>
    <script>//按鈕
        
        $("#btn_line").click(function(){
            $("#tool_detail").html(
                '<div class="line">'+
                    '<div class="row "> 原點 X<input id="start_x" /> Y<input id="start_y" /></div>'+
                    '<div class="row "> 終點 X<input id="end_x" /> Y<input id="end_y" /></div>'+
                    '<input type="hidden" id="CODE" name="CODE" />'+
                    '<input type="hidden" name="ELEMENT_NAME" value="直線"/>'+
                '</div>'+
                '<input type="submit" class="btn btn-danger" value="增" onclick="return add_line();"/>'
            );
            
        });

        $("#btn_circle").click(function(){
            $("#tool_detail").html(
                '<div class="circle">'+
                    '<div class="row "> 原點 X<input id="c_start_x" /> Y<input id="c_start_y" /></div>'+
                    '<div class="row "> 半徑 R<input id="radius" /></div>'+
                    '<input type="hidden" id="CODE" name="CODE" />'+
                    '<input type="hidden" name="ELEMENT_NAME" value="圓"/>'+
                '</div>'+
                '<input type="submit" class="btn btn-danger" value="增" onclick="return add_circle();"/>'  
            );
            
        });

        $("#btn_arc").click(function(){
            $("#tool_detail").html(
                '<div class="arc">'+
                    '<div class="row "> 原點 X<input id="arc_start_x" /> Y<input id="arc_start_y" /></div>'+
                    '<div class="row "> 半徑 R<input id="arc_radius" /></div>'+
                    '<div class="row "> 起始角<input id="arc_r_start" /> 結束角<input id="arc_r_end" /></div>'+
                    '<input type="hidden" id="CODE" name="CODE" />'+
                    '<input type="hidden" name="ELEMENT_NAME" value="弧"/>'+
                '</div>'+
                '<input type="submit" class="btn btn-danger" value="增" onclick="return add_arc();"/>'
            );
            
        });

        $("#btn_del").click(function(){
            var del_option ="";
            for (var i in php_element){
                del_option += '<option value="'+ php_element[i]["SN"]+'">' + php_element[i]["CODE"] + '</option>'
            }

            $("#tool_detail").html(
                '<select name="SN" multiple>'+
                    del_option+
                '</select>'+
                '<input type="submit" class="btn btn-danger" value="確定" name="DEL"/>'+
                '<input type="button" class="btn btn-default" value="取消" onclick="window.location.href = window.location.pathname + window.location.search;"/>'
            );
        });

    </script>
    
    <script type="text/javascript">
        $(function() {
            
            $("body").removeClass("side-by-side");
            // $("#rendering-options-menu").hide();
        });
    </script>
    
    <script type="text/javascript">
        function all_check()
        {
            allfill = true;
            $("#tool_detail").find("input").each(function(){
                if (!$(this).val()) {
                    allfill = false;
                    $(this).addClass("not_fill");
                            
                } else {
                    $(this).removeClass("not_fill");
                }
            });
            
            return allfill;
            
        }
    </script>
</body>

</html>