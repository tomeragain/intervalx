
function sendInterval() {
    var start = document.getElementById('start-date').value;
    var end = document.getElementById('end-date').value;
    var price = document.getElementById('price').value;
    if ((start === '' || start === '0') || (end === '' || end === '0') || (price === '' || price === '0')) {
        alert('Please entry all the fields to proceed with the request');
        return false;
    }
    var data = {
        data: {
            start,
            end,
            price
        }
    };
    axios.post('http://localhost/intervalx/api/index.php/api/add', data).then(function (response) {
        alert(response.data.response);
        _cleanForm();
        _cleanTable()
        getIntervals();

    }).catch(function (err) {
        console.dir(err);
    });
}

function getIntervals() {
    document.getElementById('loading-data').style.display = 'block';
    axios.get('http://localhost/intervalx/api/index.php/api/get').then(function(response) {
        document.getElementById('loading-data').style.display = 'none';
        document.getElementById('interval-data').style.display = 'block';

        var table = document.querySelector('table');
        _drawTable(table, response.data);

    }).catch(function (err) {
        console.dir(err);
    });
}

function wipeData() {
    axios.get('http://localhost/intervalx/api/index.php/api/flush').then(function(response) {
        console.log(response);
        _cleanTable();
        alert(response.data.response);
    }).catch(function (err) {
        console.dir(err);
    });
}

function _drawTable(table, data) {
    if (data.length < 0) {
        return false;
    }
    for (var element of data) {
        var row =  table.insertRow();
        for (var key in element ) {
            var cell = row.insertCell();
            var text = document.createTextNode(element[key]);
            cell.appendChild(text);
        }
        // Cell for actions
        var cellX = row.insertCell();
        var span = document.createElement('span');
        console.dir(element);
        span.innerHTML = '<i class="material-icons action" <i class="material-icons action" onclick="deleteInterval('+ element.id +')">delete</i>';
        cellX.appendChild(span);
    }
}

function deleteInterval(id) {
    var data = {
        data: {
            id
        }
    };
    axios.post('http://localhost/intervalx/api/index.php/api/delete', data).then(function (response) {
        _cleanTable();
        getIntervals();
        alert(response.data.response);
    }).catch(function (err) {
        console.dir(err);
    });
}


function _cleanTable() {
    var table = document.getElementById("interval-data-table");
    for(var i = table.rows.length - 1; i > 0; i--) {
        table.deleteRow(i);
    }
}

function _cleanForm() {

    document.getElementById('start-date').value = '';
    document.getElementById('end-date').value = '';
    document.getElementById('price').value = '';
}