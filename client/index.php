<html>
<head>
    <title>Intervals</title>
    <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="src/css/main.css">
</head>

<body onload="getIntervals()">
    <header class="header">
        <h1>Intervals <i class="material-icons" style="font-size: 32px;">cached</i></h1>
    </header>

    <main class="main-container">
        <div class="left-action-container">
            <div class="interval-action">
                <div>
                    <input id="start-date" type="number" placeholder="start day">
                    <input id="end-date" type="number" placeholder="end day">
                    <input id="price" type="number" placeholder="price">
                </div>
                <div style="padding-top: 3%">
                    <button class="submit" onclick="sendInterval()">
                        Submit
                    </button>
                </div>
            </div>

            <div>
                <div style="margin-top: 15%; ">
                    <button class="warning" onclick="wipeData()">
                        Wipe data
                    </button>
                </div>
            </div>
        </div>

        <div class="right-data-table">
            <div style="padding-left: 35%" id="loading-data">
                <img src="src/image/loading" alt="">
            </div>
            <div id="interval-data" style="display: none">
                <div>
                    <table id="interval-data-table">
                        <thead>
                        <th>Id</th>
                        <th>price</th>
                        <th>Start date</th>
                        <th>End date</th>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <footer>

    </footer>
    <script  src="https://unpkg.com/axios/dist/axios.min.js"></script>
    <script src="src/js/main.js"></script>
</body>


</html>