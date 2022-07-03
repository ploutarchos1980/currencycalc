<template>
    <div>
        <div class="modal fade" id="createFormId" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticCreateLabel" aria-hidden="false">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="staticCreateLabel">Create new record</h5>
                    </div>
                    <div class="modal-body">

                            <div class="mb-3">
                                <label for="createFromCoin" class="form-label">From coin</label>
                                <input :class="{'is-valid': isValidFrom, 'is-invalid': !isValidFrom }" aria-describedby="validationFromValid validationFromInValid" v-model="createFromCoin" type="text" class="form-control" id="createFromCoin" required placeholder="Name of the 1rst coin">
                                <div id="validationFromValid" class="valid-feedback">Great! Looks good!</div>
                                <div id="validationFromInValid" class="invalid-feedback">Need to be feeled! Can not be empty.</div>
                            </div>
                            <div class="mb-3">
                                <label for="createToCoin" class="form-label">To coin</label>
                                <input :class="{'is-valid': isValidTo, 'is-invalid': !isValidTo }" aria-describedby="validationToValid validationToInValid" v-model="createToCoin" type="text" class="form-control" id="createToCoin" required placeholder="Name of the 2rst coin">
                                <div id="validationToValid" class="valid-feedback">Great! Looks good!</div>
                                <div id="validationToInValid" class="invalid-feedback">Need to be feeled! Can not be empty.</div>
                            </div>
                            <div class="mb-3">
                                <label for="createRate" class="form-label" >Rate</label>
                                <input :class="{'is-valid': isValidRate, 'is-invalid': !isValidRate }" aria-describedby="validationRateValid validationRateInValid" @keypress="onlyNumber" @change="fixNumber" v-model="createRate" type="text" class="form-control" id="createRate" required placeholder="0.0000">
                                <div id="validationRateValid" class="valid-feedback">Great! Looks good!</div>
                                <div id="validationRateInValid" class="invalid-feedback">Need to be feeled! Can not be empty. Must be a number.</div>
                            </div>

                    </div>
                    <div class="modal-footer">
                        <button @click="closeModal" type="button" class="btn btn-secondary">Exit</button>
                        <button @click="saveData" type="button" class="btn btn-primary">Save</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    const API_ACTIONCOINSCREATE_URL = `/index.php/dashboard/action/create`


export default { 
        props: { 
            
        },
        data() {
            return {
                    createFromCoin: '',
                    createToCoin: '',
                    createRate: '',
                    modal: null,
                    isValidFrom: false,
                    isValidTo: false,
                    isValidRate: false
                };
            },
            watch: {
                createFromCoin: function(val) {
                    (val !== '' ? this.isValidFrom = true : this.isValidFrom = false);
                },
                createToCoin: function(val) {
                    (val !== '' ? this.isValidTo = true : this.isValidTo = false);
                },
                createRate: function(val) {
                    (val !== '' ? this.isValidRate = true : this.isValidRate = false);
                },
            }, 
            computed: {
                createRate: {
                    get:function()  {
                        return this.oldNum;
                    },
                    set:function(newValue) {
                        
                        this.oldNum = newValue.replace(/[^\d]/g,'');
                    }
                }
            },
            methods: {
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
                        this.createRate = fixValue.toString().replace(',','.');
                    }
                },
                closeModal() {
                    this.modal.hide();
                    this.$emit('close');
                },
                async saveData() {
                    
                    let validation = this.isValidFrom && this.isValidTo && this.isValidRate;
                    if (!validation){ return }

                    let errorInside = {
                        state: true, 
                        msg: 'Something went wrong during save. Please try again'
                    } ;
 
                    const postData = {
                        fromCoin: this.createFromCoin,
                        toCoin: this.createToCoin,
                        rate: this.createRate
                    };
                    try {
                        const response = await fetch(`${API_ACTIONCOINSCREATE_URL}`, {
                            method: "post",
                            headers: {
                                    "Content-Type": "application/json"
                            },
                            body: JSON.stringify(postData)
                        });
                        if (response.ok) {
                            const data = await response.json();
                            if (data.STATUS) {
                                if (data.STATUS == 'DONE') {
                                errorInside.state = false;
                                errorInside.msg = 'Record saved successfully.';
                                }
                                else if (data.STATUS == 'EXIST') {
                                errorInside.msg = 'This record already exist. Save cancelled.';
                                }
                            }
                        }
                    } catch (error) {
                        console.log('Error : ' + error);
                    }

                    this.modal.hide();

                    this.$emit('alert', { msg: errorInside.msg, error: errorInside.state });

                    this.$emit('close');                    
                }
            },
            computed: {

            }, 
        created() {

        },
        mounted() {
            this.modal = new bootstrap.Modal(document.getElementById('createFormId'), {});
            this.modal.show();
        }
  }

</script> 
