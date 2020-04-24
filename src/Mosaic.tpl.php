<html>
    <head>
        <title>__TITLE__</title>

        <style type="text/css">
            * {
                -webkit-box-sizing: border-box;
                -moz-box-sizing: border-box;
                box-sizing: border-box;
            }

            body {
                padding: 20px;
                font-family: Arial Narrow, Arial, sans-serif;
            }

            table.mosaic {
                table-layout: fixed;
                width: 100%;
                border-collapse: collapse;
                border-spacing: 0;
                empty-cells: show;
                border: 4px solid #CBCBCB;
            }
            table.mosaic caption {
                text-align: left;
                caption-side: top;
                text-transform: uppercase;
                font-size: x-small;
                margin-bottom: 10px;
            }
            table.mosaic tbody tr td {
                padding: 0;
                margin: 0;
                width: __CELL_WIDTH__px;
                height: __CELL_HEIGHT__px;
            }
        </style>
    </head>
    <body>
        <table class="mosaic">
            <caption>__CAPTION__</caption>

            <tbody>
                __TBODY__
            </tbody>
        </table>
    </body>
</html>
