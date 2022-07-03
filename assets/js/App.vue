<template>
    <div>

        <div v-if="no_date_entry === true" class="container mt-3">
            <div class="alert alert-warning" role="alert">
                <h4 class="alert-heading">Info!</h4>
                <p>Aww, seems there is no data to convert the selected currency. </p>
                <hr>
                <p class="mb-0">Please login to dashboard and add it. Ηowever, authorization need it to complete the above instruction.</p>
            </div>        
        </div>

        <div class="container mt-3">
            <div class="row">
                <div class="col text-end"> 
                    <button @click="goToDashboard()" type="button" class="btn"> Login Dashboard </button>
                </div>
            </div>
        </div>

        <div class="container mt-3">
            <h1>Currency Calculator</h1>
            <p>Note: If you want to add new currency you must have permisions. Please login to dashboard to enter new records.</p>

                <div class="row">
                    <div class="col p-3 bg-primary text-white">From 
                        <select v-model="fromSelected" class="form-select" id="selFrom" name="selFrom">
                            <option :value="currency" :key="currency" v-for="currency in this.list">
                                {{ currency }}
                            </option>
                        </select>
                        <input @keypress="onlyNumber" @change="fixNumber" v-model="price" type="text" class="form-control mt-2" style="font-size: 32px" id="price" name="price" placeholder="0.0000">
                    </div>
                    <div class="col p-3 bg-dark text-white">To 
                        <select v-model="toSelected" class="form-select" id="selTo" name="selTo">
                            <option :value="currency" :key="currency" v-for="currency in this.list">
                                {{ currency }}
                            </option>
                        </select>		
                    </div>
                    <div class="col p-3 bg-success text-white">Coverted 
                        <input v-model="converted" type="text" class="form-control input-lg" id="converted" placeholder="0.0000" readonly name="converted">
                        <div>{{ display_rate_text }}</div>
                    </div>
                </div>

                <div class="row mt-3 ">
                    <div class="col text-center ">
                        <button @click="reverseCoins" type="submit" class="btn btn-outline-primary me-2"> Swap </button>
                        <button @click="convertCoin" type="submit" class="btn btn-outline-primary"> <span v-if="isLoading" class="spinner-grow spinner-grow-sm"></span> Convert </button>
                    </div>
                </div>
        </div>
    </div>
</template>

<script>
    const API_CURRENCY_URL = `/index.php/currency`;
    const API_CONVERT_URL = `/index.php/currency/convert`;
    const API_DASHBOARD_URL = `/index.php/dashboard`;
    
    export default {
        data() {
            return {
                   fromSelected: '',
                   toSelected : '',
                   list: null,
                   isLoading: false,
                   price: '',
                   converted: '',
                   no_date_entry: false,
                   display_rate_text: ''
            };
        },
        watch: {
            fromSelected: function (newvalue, oldvalue) {
                if (newvalue === this.toSelected) {
                    this.fromSelected = oldvalue;
                }
            },
            toSelected: function (newvalue, oldvalue) {
                if (newvalue === this.fromSelected) {
                    this.toSelected = oldvalue;
                }
            }
        },
        computed: {

        }, 
        methods: {
            reverseCoins () {
                const ke1 = this.fromSelected;
                const ke2 = this.toSelected;

                this.fromSelected = ke2;
                this.toSelected = ke1;
            },
            onlyNumber (event) {
                let keyCode = (event.keyCode ? event.keyCode : event.which);
                if ((keyCode < 48 || keyCode > 57) && keyCode !== 46) { 
                    event.preventDefault();
                }
            },
            fixNumber(event) {
                if (event.target.value != '') {
                    let fixValue = parseFloat(event.target.value);
                    fixValue = fixValue.toFixed(4);
                    this.price = fixValue.toString().replace(',','.');
                }
            },
            async fetchData() {
                const url = `${API_CURRENCY_URL}`;

                this.list = await (await fetch(url)).json();

                if (this.list.length > 1)
                {
                    this.fromSelected = this.list[0];
                    this.toSelected = this.list[1];
                }
            },
            async convertCoin() {
                if (!this.price) { return } 

                this.no_date_entry = false;

                this.isLoading = true;

                const url = `${API_CONVERT_URL}?from=${this.fromSelected}&to=${this.toSelected}`;
                let currency = await (await fetch(url, { method: 'GET' } )).json();

                if (currency.STATUS) {
                    if (currency.STATUS == 'DONE') {
                        let calculate = parseFloat(this.price) * parseFloat(currency.CONVERTED_VALUE);
                        this.converted = calculate.toFixed(4).toString().replace(',','.');

                        let rate = parseFloat(currency.CONVERTED_VALUE).toFixed(4);
                        this.display_rate_text = `1 ${this.fromSelected} = ${rate} ${this.toSelected}`;
                    }
                    else
                    {
                        this.no_date_entry = true;
                    }
                }

                this.isLoading = false;
            },
            goToDashboard() {
               location.href = API_DASHBOARD_URL;
            }
        },
        created() {
            this.fetchData()
        }
  }

</script> 
