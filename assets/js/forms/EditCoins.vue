<template>
    <div>
        <div class="modal fade" id="editFormId" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticEditLabel" aria-hidden="false">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="staticEditEditLabel">Edit record</h5>
                    </div>
                    <div class="modal-body">

                            <div class="mb-3">
                                <label for="editFromCoin" class="form-label">From coin</label>
                                <input :class="{'is-valid': isValidFrom, 'is-invalid': !isValidFrom }" aria-describedby="validationFromValid validationFromInValid" v-model="editFromCoin" type="text" class="form-control" id="editFromCoin" required placeholder="Name of the 1rst coin">
                                <div id="validationFromValid" class="valid-feedback">Great! Looks good!</div>
                                <div id="validationFromInValid" class="invalid-feedback">Need to be feeled! Can not be empty.</div>
                            </div>
                            <div class="mb-3">
                                <label for="editToCoin" class="form-label">To coin</label>
                                <input :class="{'is-valid': isValidTo, 'is-invalid': !isValidTo }" aria-describedby="validationToValid validationToInValid" v-model="editToCoin" type="text" class="form-control" id="editToCoin" required placeholder="Name of the 2rst coin">
                                <div id="validationToValid" class="valid-feedback">Great! Looks good!</div>
                                <div id="validationToInValid" class="invalid-feedback">Need to be feeled! Can not be empty.</div>
                            </div>
                            <div class="mb-3">
                                <label for="editRate" class="form-label" >Rate</label>
                                <input :class="{'is-valid': isValidRate, 'is-invalid': !isValidRate }" aria-describedby="validationRateValid validationRateInValid" @keypress="onlyNumber" @change="fixNumber" v-model="editRate" type="text" class="form-control" id="editRate" required placeholder="0.0000">
                                <div id="validationRateValid" class="valid-feedback">Great! Looks good!</div>
                                <div id="validationRateInValid" class="invalid-feedback">Need to be feeled! Can not be empty. Must be a number.</div>
                            </div>

                    </div>
                    <div class="modal-footer">
                        <button @click="closeEditModal" type="button" class="btn btn-secondary">Exit</button>
                        <button @click="saveEditData" type="button" class="btn btn-primary">Save Changes</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</template>

<script>
    const API_ACTIONCOINSUPDATE_URL = `/index.php/dashboard/action/update`


export default { 
    props: { 
        parentData: {
            required: true,
            type: Object,
            },
        },
        data() {
            return {
                    editId: '',
                    editFromCoin: '',
                    editToCoin: '',
                    editRate: '',
                    modal: null,
                    isValidFrom: true,
                    isValidTo: true,
                    isValidRate: true,
                };
            },
            watch: {
                editFromCoin: function(val) {
                    (val !== '' ? this.isValidFrom = true : this.isValidFrom = false);
                },
                editToCoin: function(val) {
                    (val !== '' ? this.isValidTo = true : this.isValidTo = false);
                },
                editRate: function(val) {
                    (val !== '' ? this.isValidRate = true : this.isValidRate = false);
                },
            },
            computed: {
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
                        this.editRate = fixValue.toString().replace(',','.');
                    }
                },
                closeEditModal() {
                    this.modal.hide();
                    this.$emit('close');
                },
                async saveEditData() {
                    let validation = this.isValidFrom && this.isValidTo && this.isValidRate;
                    if (!validation){ return }

                    let errorInside = {
                        state: true, 
                        msg: 'Something went wrong during update. Please try again'
                    };
                    const postData = {
                        coinid: this.editId,
                        fromCoin: this.editFromCoin,
                        toCoin: this.editToCoin,
                        rate: this.editRate
                    };
                    try {
                        const response = await fetch(`${API_ACTIONCOINSUPDATE_URL}`, {
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
                                errorInside.msg = 'Record updated successfully.';
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
            this.editId = this.parentData.coinId;
            this.editFromCoin = this.parentData.fromCoin;
            this.editToCoin = this.parentData.toCoin;
            this.editRate = this.parentData.rate;
        },
        mounted() {
            this.modal = new bootstrap.Modal(document.getElementById('editFormId'), {});
            this.modal.show();
        }
  }

</script> 
