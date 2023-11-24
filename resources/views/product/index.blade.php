@extends('adminlte::page')
@section('title',  $pageTitle)

@section('content_header')
    <h1>{{$pageTitle}}</h1>
@stop

@section('content')
<div class="container position-relative">
    <div class="row table-preloader"  v-if="isTableLoading">
        <div class="col p-5 text-center text-dark">
            <i class="fas fa-spin fa-circle-notch fa-3x"></i>
            <p>Loading records</p>
        </div>
    </div>
    <table class="table table-sm table-light table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Product Name</th>
                <th>Category</th>
                <th>Date</th>
                <th class="text-center">Actions</th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="product in products">
                <td>@{{ product.id }}</td>
                <td>@{{ product.name }}</td>
                <td>@{{ product.category }}</td>
                <td>@{{ product.date }}</td>
                <td class="text-center">
                    <div class="btn-group">
                        <a :href="product.routes.edit" class="btn btn-light btn-sm text-primary"><i class="fas fa-pen"></i></a>
                        <button class="btn btn-sm text-danger btn-light " @click="deleteProduct(product)"><i class="fas fa-trash"></i></button>
                    </div>
                </td>
            </tr>
        </tbody>
        <tfoot v-if="products">
            <tr>
                <td colspan="5">
                    <div class="input-group d-flex justify-content-center">
                        <div v-if="links.first" class="input-group-prepend">
                            <button :href="links.first" class="btn btn-sm btn-light btn-outline-light text-dark" @click="changePage($event, 1)">First</button>
                        </div>
                        <select class="form-control" @change="changePage($event)" v-model="current_page" style="max-width: 100px;">
                            <option v-for="n in meta.last_page" :value="n">
                                Page @{{ n }}
                            </option>
                        </select>
                        <div v-if="links.last" class="input-group-append">
                            <button :href="links.last" class="btn btn-sm btn-light btn-outline-light text-dark" @click="changePage($event, meta.last_page)">Last</button>
                        </div>
                    </div>

                </td>
            </td>
        </tfoot>
    </table>
</div>
@stop

@section('css')
<style>
.table-preloader{
    position: absolute;
    z-index: 999;
    background: #ffffffbd;
    width: 100%;
    height: 100%;
}
table{
    min-height: 100px;
}
</style>
@stop
@section('js')
<script>
const { createApp } = Vue
vueTable = createApp({
    data() {
        return {
            isTableLoading : true,
            products : {},
            links: {},
            meta: {},
            current_page:1,
        }
    },
    methods : {
        getProducts(){
            vueTable.isTableLoading = true;
            axios.get(`{{ route('api.product.data') }}?page=${vueTable.current_page}`,{
                headers: {
                    'Authorization': 'Bearer {{ $apiToken }}'
                }
            })
            .then(response => {
                vueTable.links = response.data.links;
                vueTable.meta = response.data.meta;
                vueTable.products = response.data.data;
                vueTable.current_page = response.data.meta.current_page;
                vueTable.isTableLoading = false;
            });
        },

        deleteProduct(product) {
            Swal.fire({
                // title: `Are you sure you want to delete ${product.name} ?`,
                text: `Are you sure you want to delete ${product.name} ?`,
                icon: "question",
                preConfirm:function(){
                    axios.delete(product.routes.edit,{
                        headers: {
                            'Authorization': 'Bearer {{ $apiToken }}'
                        }
                    })
                    .then(response => {
                        Toast.fire({
                            icon: response.data.type,
                            title: response.data.message
                        });
                        vueTable.getProducts();
                    });
                }
            });
        },

        changePage(event, page = null){
            if(page)
                vueTable.current_page = page;

            vueTable.getProducts();
        }

    }
}).mount('.content')
vueTable.getProducts();

</script>
@stop
