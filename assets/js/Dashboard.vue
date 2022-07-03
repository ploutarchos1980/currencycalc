<template>
    <div>

        <div v-if="message != null" id="alertbox" class="container">
            <div id="alertmessage" :class="alertType" class="alert alert-dismissible fade show" role="alert">
                <strong> {{ message }} </strong>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>

        <div class="container-lg">
            <div class="table-responsive">
                <div class="table-wrapper">
                    <div class="col text-end dashwrap"> 
                        <button @click="goLogout()" type="button" class="btn dashbutton"> Logout </button>
                    </div>

                    <create-coins v-if="createWindowState" @close="closeCreateWindow" @alert="showAlert"></create-coins>

                    <edit-coins v-if="editWindowState" @close="closeEditWindow" :parent-data="bindData" @alert="showAlert"></edit-coins>

                    <delete-coins v-if="deleteWindowState" @close="closeDeleteWindow" :parent-data="bindData" @alert="showAlert"></delete-coins>
                    
                    <div class="table-title">
                        <div class="row">
                            <div class="col-sm-8"><h2>Currency <b>Records</b></h2></div>
                            <div class="col-sm-4">
                                <button @click="createCoin()" type="button" class="btn btn-info add-new"><i class="fa fa-plus"></i> Add New Record</button>
                            </div>
                        </div>
                    </div>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>From Coin</th>
                                <th>To Coin</th>
                                <th>Rate</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                             <tr v-for="currency in currencyList" :key="currency.id">
                                <td>{{currency.fromCoin}}</td>
                                <td>{{currency.toCoin}}</td>
                                <td>{{ currency.rate = fixNumber(currency.rate) }}</td>
                                <td>
                                    <a @click="editCoin( currency )" class="edit" title="Edit" ><i class="material-icons">&#xE254;</i></a>
                                    <a @click="deleteCoin( currency )" class="delete" title="Delete" ><i class="material-icons">&#xE872;</i></a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div> 
    </div>
</template>

<script>
    import CreateCoins from "./forms/CreateCoins.vue";
    import EditCoins from "./forms/EditCoins.vue";
    import DeleteCoins from "./forms/DeleteCoins.vue";

    const API_LISTCOINS_URL = `/index.php/dashboard/getcoins`;
    const API_LOGOUT_URL = '/index.php/logout';


    export default {
        components: { DeleteCoins, CreateCoins, EditCoins }, 
        data() {
            return {
                 currencyList: [],
                 deleteWindowState: false,
                 bindData: {
                    fromCoin: '',
                    toCoin: '',
                    coinId: '',
                    coinRate: ''
                 },
                createWindowState: false,
                editWindowState: false,
                alertType: 'alert-primary',
                message: null
            };
        },
        computed: {
           
        }, 
        methods: {
            fixNumber(value) {
                let fixValue = 0;

                if (value != '') {
                    fixValue = parseFloat(value);
                    fixValue = fixValue.toFixed(4);
                }

                return fixValue.toString().replace(',','.');
            },
            async fetchData() {
                const url = `${API_LISTCOINS_URL}`;

                this.currencyList = await (await fetch(url)).json();
            },
            updateData ( currency ) {
                this.bindData.fromCoin = currency.fromCoin;
                this.bindData.toCoin = currency.toCoin;
                this.bindData.coinId = currency.id;
                this.bindData.rate = currency.rate;
            },
            emptyData() {
              this.bindData.fromCoin = '';
              this.bindData.toCoin = '';
              this.bindData.coinId = '';
              this.bindData.rate = '';
            },
            goLogout() {
                location.href = API_LOGOUT_URL;
            },
            createCoin() {
                this.createWindowState = true;
            },
            closeCreateWindow() {
                this.createWindowState = false;
            },
            deleteCoin(currency) {
                this.updateData(currency);
                this.deleteWindowState = true;
            },
            closeDeleteWindow() {
              this.deleteWindowState = false;  
              this.emptyData();
            },
            editCoin(currency) {
                this.updateData(currency)
                this.editWindowState = true;
            },
            closeEditWindow() {
              this.editWindowState = false;  
              this.emptyData();
            },
            showAlert(data) {
                this.message = data.msg;
                this.alertType = (data.error ? 'alert-danger' : 'alert-success');

                if (!data.error) { 
                    this.fetchData();
                }

                let vm = this;
                setTimeout(function() { 
                       vm.message = null;
                    }, 3000
                );
            }
        },
        created() {
            this.fetchData();
        }
  }

</script> 
