<template>
    <div>
        <div class="modal fade" id="deleteFormId" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticDeleteLabel" aria-hidden="false">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="staticDeleteLabel">Delete record</h5>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to delete record with id {{ parentData.coinId }} ? <br>
                        From: {{ parentData.fromCoin }} - To: {{ parentData.toCoin }} - Rate: {{ parentData.rate }}
                    </div>
                    <div class="modal-footer">
                        <button @click="closeModal" type="button" class="btn btn-secondary">No</button>
                        <button @click="yesDelete" type="button" class="btn btn-primary">Yes</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    const API_ACTIONCOINSDELETE_URL = `/index.php/dashboard/action/delete`

export default { 
    props: { 
        parentData: {
            required: true,
            type: Object,
            },
        },
        data() {
            return {
                    modal: null
                };
            },
            methods: {
                closeModal() {
                    this.modal.hide();
                    this.$emit('close');
                },
                async yesDelete() {
                    
                    let errorInside = {
                        state: true, 
                        msg: 'Something went wrong during update. Please try again'
                    } ;

                    const postData = {
                        coinid: this.parentData.coinId,
                    };
                    try {
                        const response = await fetch(`${API_ACTIONCOINSDELETE_URL}`, {
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
                                errorInside.msg = 'Record deleted successfully.';
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
            this.modal = new bootstrap.Modal(document.getElementById('deleteFormId'), {});
            this.modal.show();
        }
  }

</script> 
