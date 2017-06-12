<!doctype html>
<html>
<head>
    <style>
        html,body,div,span,applet,object,iframe,h1,h2,h3,h4,h5,h6,p,blockquote,pre,a,abbr,acronym,address,big,cite,code,del,dfn,em,img,ins,kbd,q,s,samp,small,strike,strong,sub,sup,tt,var,b,u,i,center,dl,dt,dd,ol,ul,li,fieldset,form,label,legend,table,caption,tbody,tfoot,thead,tr,th,td,article,aside,canvas,details,embed,figure,figcaption,footer,header,hgroup,menu,nav,output,ruby,section,summary,time,mark,audio,video{border:0;font-size:100%;font:inherit;vertical-align:baseline;margin:0;padding:0}article,aside,details,figcaption,figure,footer,header,hgroup,menu,nav,section{display:block}body{line-height:1}ol,ul{list-style:none}blockquote,q{quotes:none}blockquote:before,blockquote:after,q:before,q:after{content:none}table{border-collapse:collapse;border-spacing:0}

        html {
            background: rgb(20, 65, 177);
            background: linear-gradient(135deg, rgb(20, 65, 177) 0%, rgba(57,135,219,1) 100%);
            background-attachment: fixed;
            font-family: 'Fira Mono', Monaco, Menlo, monospace;
            font-size: 10px;
            height: 100%;
            width: 100%;
        }

        body {
            background: url(loading.gif) no-repeat center center;
            background-size: 50px;
            align-items: center;
            box-sizing: border-box;
            justify-content: center;
            flex-direction: row;
            display: flex;
            height: 100%;
            padding: 3rem;
            width: 100%
        }

        table {
            border-collapse: collapse;
            color: #fff;
            font-size: 1.6rem;
        }

        table tr td,
        table tr th {
            padding: 1.5rem;
        }

        table tr td:first-child,
        table tr th:first-child {
            border-left: 0;
        }

        table th {
            border-bottom: 2px solid rgba(255, 255, 255, 0.5);
        }

        table tr:last-child td {
            border-top: 2px solid rgba(255, 255, 255, 0.5);
        }

        table .num {
            font-weight: 700;
            text-align: right;
        }

        table .pos {
            color: #4dff97;
        }

        table .neg {
            color: #ff5658;
        }

        @media screen and (max-width: 800px) {
            body {
                display: block;
            }

            table {
                width: 100%;
            }

            table tr {
                border-radius: 0.5rem;
                box-sizing: border-box;
                display: block;
                margin-bottom: 1rem;
                overflow: hidden;
                padding: 1rem;
                width: 100%;
            }

            table tr:first-child {
                display: none;
            }

            table tr td {
                border: 0;
                box-sizing: border-box;
                color: #fff;
                display: block;
                float: left;
                text-align: center;
            }

            table tr .num {
                text-align: center;
            }

            table tr td::before {
                color: #fff;
                content: attr(data-field);
                display: block;
                font-weight: 400;
                margin-bottom: 1rem;
            }

            table tr td span {
                display: none;
            }

            table tr td[data-field="Symbol"]::before,
            table tr td[data-field="Value"]::before,
            table tr td[data-field="Profit"]::before {
                display: none;
            }

            table tr td[data-field="Symbol"] {
                border-bottom: 1px solid #ccc;
                font-size: 2rem;
                font-weight: 700;
                line-height: 2rem;
                text-align: left;
                width: 28%;
            }

            table tr td[data-field="Value"],
            table tr td[data-field="Profit"] {
                border-bottom: 1px solid #ccc;
                line-height: 2rem;
                text-align: right;
                width: 36%;
            }

            table tr td[data-field="Invested"],
            table tr td[data-field="1 hr"],
            table tr td[data-field="24 hrs"],
            table tr td[data-field="7 days"] {
                background: rgba(255, 255, 255, 0.1);
                font-size: 1.2rem;
                width: 25%
            }

            table tr td[data-field="1 hr"].neg,
            table tr td[data-field="24 hrs"].neg,
            table tr td[data-field="7 days"].neg {
                color: #ff8e93;
            }

            table tr.total td {
                border-top: 0;
            }

            table tr.total td[data-field="Invested"],
            table tr.total td[data-field="1 hr"],
            table tr.total td[data-field="24 hrs"],
            table tr.total td[data-field="7 days"] {
                display: none;
            }
        }
    </style>
    <title>Coin Status</title>
    <meta name="viewport" content="width=device-width,user-scalable=no,initial-scale=1">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Fira+Mono:400,500,700">
</head>
<body>
    <script>
        var Coins = (function(document) {
          var INTERVAL = 60000;
          var element, timeout;

          function init(elementSelector) {
            element = document.querySelector(elementSelector);

            fetchData(displayData);
          }

          function fetchData(cb)
          {
            var xmlHttp = new XMLHttpRequest();

            xmlHttp.open('GET', 'data.php');

            xmlHttp.onreadystatechange = function() {
              if (xmlHttp.readyState === 4 && xmlHttp.status === 200) {
                cb(xmlHttp.responseText);
              }
            };

            xmlHttp.send(null);

            start();
          }

          function displayData(data) {
            data = JSON.parse(data);

            var container = document.createElement('table');
            var totalValue = 0.0;
            var totalInvestment = 0.0;

            container.appendChild(tableRow([
              ['', '', 'Symbol'],
              ['', '', 'Profit'],
              ['', '', 'Value'],
              ['', '', 'Invested'],
              ['', '', '1 hr'],
              ['', '', '24 hrs'],
              ['', '', '7 days']
            ], true));

            data.forEach(function(coin) {
              totalValue += coin.value;
              totalInvestment += coin.investment;

              container.appendChild(tableRow([
                ['text', 'Symbol', coin.symbol],
                ['percentage', 'Profit', ((coin.value - coin.investment) / coin.investment) * 100],
                ['currency', 'Value', coin.value],
                ['currency', 'Invested', coin.investment],
                ['percentage', '1 hr', coin.change['1hr']],
                ['percentage', '24 hrs', coin.change['24hr']],
                ['percentage', '7 days', coin.change['7d']]
              ], false));
            });

            container.appendChild(tableRow([
              ['text', 'Symbol', 'TOTAL'],
              ['percentage', 'Profit', (totalValue - totalInvestment) / totalInvestment * 100],
              ['currency', 'Value', totalValue],
              ['currency', 'Invested', totalInvestment],
              ['', '1 hr', ''],
              ['', '24 hrs', ''],
              ['', '7 days', '']
            ], false, 'total'));

            element.innerHTML = '';
            element.style.background = 'none';
            element.appendChild(container);
          }

          function tableRow(values, isHeader = false, additionalClass = '') {
            var row = document.createElement('tr');

            if (additionalClass !== '') {
              row.classList.add(additionalClass);
            }

            var cell = document.createElement(isHeader ? 'th' : 'td');

            values.forEach(function(value) {
              var el = cell.cloneNode(false);

              el.setAttribute('data-field', value[1]);

              switch(value[0]) {
                case 'currency':
                  el.classList.add('num');
                  el.innerHTML = '&euro;&nbsp;' + value[2].toFixed(2);
                  break;
                case 'percentage':
                  el.classList.add('num');
                  el.classList.add(parseFloat(value[2]) > 0 ? 'pos' : 'neg');
                  el.innerHTML = percentage(value[2]);
                  break;
                case 'text':
                default:
                  el.innerHTML = value[2];
              }

              row.appendChild(el);
            });

            return row;
          }

          function percentage(value) {
            value = parseFloat(value);
            return parseFloat(value) > 0 ? '<span>+&nbsp;</span>' + value.toFixed(2) + '%' : '<span>-&nbsp;</span>' + Math.abs(value.toFixed(2)) + '%';
          }

          function pause() {
            clearTimeout(timeout);
          }

          function start() {
            timeout = setTimeout(function() {
              fetchData(displayData);
            }, INTERVAL);
          }

          return {
            init: init,
            pause: pause,
            start: start
          }
        })(document);

        Coins.init('body');
    </script>
</body>
</html>
