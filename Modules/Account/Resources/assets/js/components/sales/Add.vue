<template lang="">
    <div>
        <!-- breadcrumb -->
        <div class="row">
            <div class="col-md-6">
                <h4>All Sale</h4>
            </div>
            <div class="col-md-6 text-right">
                <a href="" class="btn btn-success">Add Sale</a>
            </div>
        </div>

        <!-- body -->
        <div class="card mt-2">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">

                        <div class="form-group">
                            <label>Customer <span class="req_star">*</span> </label>
                            <select class="form-control" name="cus_auto_id">
                                <option value=""> ---------- Select One ---------- </option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Invoice No <span class="req_star">*</span> </label>
                            <input class="form-control" placeholder="Invoice No... " />
                        </div>

                        <div class="form-group">
                            <label>Date<span class="req_star">*</span> </label>
                            <input class="form-control" placeholder="Invoice No... " />
                        </div>


                    </div>
                </div>


            </div>
        </div>



    </div>
</template>
<script>
import Form from 'vform';

export default {
    props: {
        data: {
            type: Object,
            required: true
        }
    },

    data(){
        return {
            form: new Form({
                cus_auto_id : '',
                sr_invoice_no : '',
                sr_invoice_description : '',
                sr_tnx_id : '',
                sr_issue_date : '',
                sr_payment_terms : '',
                sr_due_date : '',
                sr_supply_date : '',
                sr_payment_mean : '',
                sr_total_amount : '',
                sr_discount_amount : '',
                sr_vat_amount : '',
                sr_grand_total_amount : '',
                sale_items: [],

            })
        }
    },

    methods: {
        /* =================== Add To OrderList ===================== */
        addToOrderList(product) {
            let temp_item = {
                // product information
                spi_auto_id : product.spi_auto_id,
                spi_name_en: product.spi_name_en,
                spi_code : product.spi_code,

                srd_description : '',
                srd_qty : 0.00,
                srd_unit_price : 0.00,
                srd_inclusive: false,
                srd_discount: 0.00,
                srd_vat_percent: '',
                srd_vat_value: 0.00,
                srd_total_amount: 0.00,


                variant_id: product.variants[0].id,
                price: product.variants[0].pivot.sale_price,
                stock: product.variants[0].pivot.stock,
                quantity: 1,
                // product information
                variants: [],
            };



            product.variants.map((v_item) => {
                let variant_item = {
                    id: v_item.id,
                    name: v_item.name,
                    stock: v_item.pivot.stock,
                    price: v_item.pivot.sale_price,
                };
                temp_item.variants.push(variant_item);
            });



            if (this.form.sale_items.filter((e) => e.id === temp_item.id).length < 1 ) {
                this.form.sale_items.push(temp_item);
            }
            else{
                toast.warning('this item already exit!')
                return;
            }


            this.totalCalculator();

        },


        /* ===================== set stock and price ===================== */
        setPriceAndStock(item,index){
            item.variants.map((v_item) => {
                if(v_item.id === item.variant_id){
                    item.price = v_item.price,
                    item.stock = v_item.stock,
                    item.quantity = 1
                }
            })
            this.totalCalculator();
        },



        resetOrderItem() {
            this.form.sale_items = [];
            this.form.total_amount = 0;
            this.form.paid_amount = 0;
            this.form.due = 0;
            return;
        },

        removeItem(product_id) {
            this.form.sale_items = this.form.sale_items.filter(item => item.id !== product_id);
            this.totalCalculator();
        },


        /* ====================== Calculation ====================== */
        totalCalculator() {
            clearTimeout(this.timeout);
            this.timeout = setTimeout(() => {
                // start Logic
                let products = this.form.sale_items;

                products.map((item) => {
                    if(item.stock < item.quantity) {
                        toast.warning(item.quantity + ' quantity not available');
                        item.quantity = item.stock;
                        return
                    }
                })


                let total = 0;
                let discount =
                    this.form.discount == "" ? 0 : parseFloat(this.form.discount);
                let paid = this.form.paid_amount == "" ? 0 : parseFloat(this.form.paid_amount);

                for (let i = 0; i < products.length; i++) {
                    total +=
                        (products[i].price == "" ? 0 : parseFloat(products[i].price)) *
                        (products[i].quantity == "" ? 0 : parseInt(products[i].quantity));
                }

                if (paid + discount > parseFloat(total)) {
                    toast.warning(
                        `why paid amount ${paid} and discount amount ${discount} bigger this total amount ${total} ?????`,
                        {
                            type: "danger",
                            position: "top-right",
                            duration: 4000,
                        },
                    );
                    this.form.paid_amount = total;
                    this.form.discount = 0;
                    this.form.due =
                        parseFloat(total) -
                        (parseFloat(this.form.paid_amount) + parseFloat(this.form.discount) ?? 0);
                    return;
                }
                this.form.total_amount = parseFloat(total).toFixed(2);

                this.form.due =
                    this.form.total_amount -
                    parseFloat(discount) -
                    parseFloat(paid)
                // end logic
            }, this.delay);
        },

        /* ========================= end methods ========================= */



    }

}
</script>