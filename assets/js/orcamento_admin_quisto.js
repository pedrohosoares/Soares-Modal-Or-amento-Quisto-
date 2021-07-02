let table_soares_orcamento = {
    table: document.querySelector('table'),
    thead: document.querySelector('table thead'),
    tbody: document.querySelector('table tbody'),
    tfoot: document.querySelector('table tfoot'),
    anterior: document.querySelector('table tfoot button#prev'),
    proximo: document.querySelector('table tfoot button#next'),
    paginationNumber: document.querySelector('table tfoot td.pages'),
    limit: 19,
    paginas: 0,
    page: 0,
    min: 0,
    max: 20,
    totalData: 0,
    json: null,
    dataSearchTable:{},
    ajax(url) {
        fetch(url,{
            method:"POST",
            credentials: 'same-origin',
            headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'Cache-Control': 'no-cache',
            },
            body:new URLSearchParams({
                action: data.action
            })
        })
            .then(response => response.json())
            .then((json) => {
                table_soares_orcamento.json = json;
                table_soares_orcamento.totalData = json.length;
                table_soares_orcamento.rendertable(0, 20);
                table_soares_orcamento.clickNext();
                table_soares_orcamento.clickPrev();
            });

    },
    limitContentPages(min, max) {
        return table_soares_orcamento.json.filter((v, i) => {
            return i >= min && i < max;
        })
    },
    insertColunsTable(data, newTR) {
        let newTD = newTR.insertCell();
        let newText = document.createTextNode(data);
        newTD.appendChild(newText);

    },
    
    ajaxFilterField(nameField, textFilter) {
        table_soares_orcamento.dataSearchTable[nameField] = textFilter;
        console.log(table_soares_orcamento.dataSearchTable);
        fetch(data.ajaxurl, {
            method: "POST",
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'Cache-Control': 'no-cache',
            },
            body: new URLSearchParams({
                action: "ajax_soares_orcamento_admin_filtro_fields",
                data: JSON.stringify(table_soares_orcamento.dataSearchTable)
            })
        })
        .then(r => r.json())
        .then((r) => {
                table_soares_orcamento.json = r;
                table_soares_orcamento.totalData = r.length;
                table_soares_orcamento.rendertableBody(0, 20);
                table_soares_orcamento.clickNext();
                table_soares_orcamento.clickPrev();
        });
    },
    changeFieldSearchTable(input) {
        let timeout = null;
        input.onkeyup = (r) => {
            let textInput = r.target.value;
            let name = r.target.name.split('_')[1];
            clearTimeout(timeout);
            timeout = setTimeout(function () {
                table_soares_orcamento.ajaxFilterField(name, textInput);
            }, 1000);
        }
    },
    insertFieldSearchTable(trName, newTR) {
        let newTD = newTR.insertCell();
        let input = document.createElement('input');
        input.style.width = "100%";
        input.name = "_" + trName;
        newTD.appendChild(input);
        this.changeFieldSearchTable(input);

    },
    clickNext() {
        table_soares_orcamento.proximo.onclick = (e) => {
            if (table_soares_orcamento.page >= (table_soares_orcamento.paginas - 1)) {
                return;
            }
            table_soares_orcamento.page += 1;
            table_soares_orcamento.min += 20;
            table_soares_orcamento.max += 20;
            table_soares_orcamento.rendertable(table_soares_orcamento.min, table_soares_orcamento.max);

        };
    },
    clickPrev() {
        table_soares_orcamento.anterior.onclick = (e) => {
            if (table_soares_orcamento.page < 1) {
                return;
            }
            table_soares_orcamento.page -= 1;
            table_soares_orcamento.min -= 20;
            table_soares_orcamento.max -= 20;
            table_soares_orcamento.rendertable(table_soares_orcamento.min, table_soares_orcamento.max);

        };
    },
    putThead(data) {
        table_soares_orcamento.thead.innerHTML = "";
        let newTR = table_soares_orcamento.thead.insertRow();
        for (let index = 0; index < data.length; index++) {
            table_soares_orcamento.insertFieldSearchTable(data[index], newTR);
        }
        let newTR2 = table_soares_orcamento.thead.insertRow();
        for (let index = 0; index < data.length; index++) {
            table_soares_orcamento.insertColunsTable(data[index], newTR2);
        }
    },
    cE(tag){
        return document.createElement(tag);
    },
    rendertable(min, max) {
        this.tbody.innerHTML = "";
        this.paginas = table_soares_orcamento.json.length / 20;
        let newJSON = table_soares_orcamento.limitContentPages(min, max);
        if(newJSON[0] == undefined){
            return;
        }
        let keys = Object.keys(newJSON[0]);
        this.putThead(keys);
        newJSON.forEach((dados, indice) => {
            let newTR = table_soares_orcamento.tbody.insertRow();
            newTR.style.cursor = "pointer";
            for (let index = 0; index < keys.length; index++) {
                console.log(dados.serviços);
                /*
                if(dados.serviços != undefined && dados.serviços != ''){
                    dados.serviços = JSON.parse(dados.serviços);
                    console.log(dados.serviços);
                    if(dados.serviços['servico'] != undefined){
                        dados.serviços = dados.serviços['servico'];
                    }else{
                        dados.serviços = '';
                    }
                }
                */
                table_soares_orcamento.insertColunsTable(dados[keys[index]], newTR);
            }
            newTR.onclick = (r)=>{
                let countTD = r.target.parentNode.querySelectorAll('td').length;
                let td = r.target.parentNode.querySelectorAll('td')[countTD-2];
                if(r.target.parentNode.querySelectorAll('td').length == 0){
                    return;
                }
                let id = r.target.parentNode.querySelectorAll('td')[0].innerText.trim();
                let franqueado = td.innerText.trim();
                if(franqueado == '' || franqueado == 'null'){
                    td.innerHTML = "";
                    td.innerHTML = "";
                
                }   
            };
        });
        this.putPages();

    },
    rendertableBody(min, max) {
        this.tbody.innerHTML = "";
        this.paginas = table_soares_orcamento.json.length / 20;
        let newJSON = table_soares_orcamento.limitContentPages(min, max);
        if (newJSON[0] == undefined) {
            return;
        }
        let keys = Object.keys(newJSON[0]);
        newJSON.forEach((dados, indice) => {
            let newTR = table_soares_orcamento.tbody.insertRow();
            newTR.style.cursor = "pointer";
            console.log(dados);
            for (let index = 0; index < keys.length; index++) {
                table_soares_orcamento.insertColunsTable(dados[keys[index]], newTR);
            }
            //Franqueado
            let id = newTR.querySelectorAll('td')[0].innerText.trim();
            newTR.onclick = (r) => {
                let countTD = r.target.parentNode.querySelectorAll('td').length;
                let td = r.target.parentNode.querySelectorAll('td')[countTD-2];
                if(r.target.parentNode.querySelectorAll('td').length == 0){
                    return;
                }
                let id = r.target.parentNode.querySelectorAll('td')[0].innerText.trim();
                let franqueado = td.innerText.trim();
                if(franqueado == '' || franqueado == 'null'){
                    td.innerHTML = "";
                    td.innerHTML = "";
                
                } 
            };
        });
        this.putPages();

    },
    sair(button,id,text){
        button.onclick = (r)=>{
            r.preventDefault();
            button.parentNode.innerHTML = text;
        };
    },
    putPages() {
        table_soares_orcamento.paginationNumber.innerText = (table_soares_orcamento.page + 1) + " de " + Math.ceil(table_soares_orcamento.paginas);
    },
    init(url) {

        this.ajax(url);
    }

}
table_soares_orcamento.init(data.ajaxurl);