<template>
  <main class="main">
    <!-- Breadcrumb -->
    <ol class="breadcrumb">
      <li class="breadcrumb-item">
        <a href="/">Escritorio</a>
      </li>
    </ol>
    <div class="container-fluid">
      <!-- Ejemplo de tabla Listado -->
      <div class="card">
        <div class="card-header">
          <i class="fa fa-align-justify"></i> Categorías
          <button
            type="button"
            @click="abrirModal('categoria', 'registrar')"
            class="btn btn-secondary"
          >
            <i class="icon-plus"></i>&nbsp;Nuevo
          </button>
        </div>
        <div class="card-body">
          <div class="form-group row">
            <div class="col-md-6">
              <div class="input-group">
                <select class="form-control col-md-3" v-model="criterio">
                  <option value="nombre">Nombre</option>
                  <option value="descripcion">Descripción</option>
                </select>
                <input
                  type="text"
                  class="form-control"
                  placeholder="Texto a buscar"
                  v-model="buscar"
                  @keyup.enter="listarCategoria(1, buscar, criterio)"
                >
                <button type="submit" @click="listarCategoria(1, buscar, criterio)" class="btn btn-primary">
                  <i class="fa fa-search"></i> Buscar
                </button>
              </div>
            </div>
          </div>
          <table class="table table-bordered table-striped table-sm">
            <thead>
              <tr>
                <th>Opciones</th>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Estado</th>
              </tr>
            </thead>
            <tbody>
              <!-- Creamos un for para listar las categorias es nuestra tabla -->
              <tr v-for="categoria in arrayCategoria" :key="categoria.id">
                <td>
                  <button
                    type="button"
                    @click="abrirModal('categoria', 'actualizar', categoria)"
                    class="btn btn-warning btn-sm"
                  >
                    <i class="icon-pencil"></i>
                  </button> &nbsp;
                  <template v-if="categoria.condicion">
                    <button
                      type="button"
                      @click="desactivarCategoria(categoria.id)"
                      class="btn btn-danger btn-sm"
                    >
                      <i class="icon-trash"></i>
                    </button>
                  </template>
                  <template v-else>
                    <button
                      type="button"
                      @click="activarCategoria(categoria.id)"
                      class="btn btn-info btn-sm"
                    >
                      <i class="icon-check"></i>
                    </button>
                  </template>
                </td>
                <td v-text="categoria.nombre"></td>
                <td v-text="categoria.descripcion"></td>
                <td>
                  <div v-if="categoria.condicion">
                    <span class="badge badge-success">Activo</span>
                  </div>
                  <div v-else>
                    <span class="badge badge-danger">Descativado</span>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
          <nav>
            <ul class="pagination">
              <li class="page-item" v-if="pagination.current_page > 1">
                <a class="page-link" @click.prevent="cambiarPagina(pagination.current_page - 1, buscar, criterio)" href="#">Ant</a>
              </li>
              <li class="page-item" v-for="page in pagesNumber" :key="page" :class="[page == isActived ? 'active' : '']">
                <a class="page-link" href="#" @click.prevent="cambiarPagina(page, buscar, criterio)" v-text="page"></a>
              </li>
              <li class="page-item" v-if="pagination.current_page < pagination.last_page" >
                <a class="page-link" @click.prevent="cambiarPagina(pagination.current_page + 1, buscar, criterio)" href="#">Sig</a>
              </li>
            </ul>
          </nav>
        </div>
      </div>
      <!-- Fin ejemplo de tabla Listado -->
    </div>
    <!--Inicio del modal agregar/actualizar-->
    <div
      class="modal fade"
      tabindex="-1"
      role="dialog"
      aria-labelledby="myModalLabel"
      style="display: none;"
      aria-hidden="true"
      :class="{'mostrar' : modal}"
    >
      <div class="modal-dialog modal-primary modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title" v-text="tituloModal"></h4>
            <button type="button" class="close" @click="cerrarModal()" aria-label="Close">
              <span aria-hidden="true">×</span>
            </button>
          </div>
          <div class="modal-body">
            <form action method="post" enctype="multipart/form-data" class="form-horizontal">
              <div class="form-group row">
                <label class="col-md-3 form-control-label" for="text-input">Nombre</label>
                <div class="col-md-9">
                  <input
                    type="text"
                    class="form-control"
                    placeholder="Nombre de categoría"
                    v-model="nombre"
                  >
                </div>
              </div>
              <div class="form-group row">
                <label class="col-md-3 form-control-label" for="email-input">Descripción</label>
                <div class="col-md-9">
                  <input class="form-control" placeholder="Descripción" v-model="descripcion">
                </div>
              </div>
              <div v-show="errorCategoria" class="form-group row div-error">
                <div class="text-center text-error">
                  <div v-for="error in errorMostrarMsjCategoria" :key="error" v-text="error"></div>
                </div>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" @click="cerrarModal()" class="btn btn-secondary">Cerrar</button>
            <button
              type="button"
              v-if="tipoAccion==1"
              @click="registrarCategoria()"
              class="btn btn-primary"
            >Guardar</button>
            <button
              type="button"
              v-if="tipoAccion==2"
              @click="actualizarCategoria()"
              class="btn btn-primary"
            >Actualizar</button>
          </div>
        </div>
        <!-- /.modal-content -->
      </div>
      <!-- /.modal-dialog -->
    </div>
    <!--Fin del modal-->
    <!-- Inicio del modal Eliminar -->
    <!-- Fin del modal Eliminar -->
  </main>
</template>

<script>
export default {
  // declaramos el data para guardar la información recibida
  data() {
    return {
      nombre : '',
      descripcion : '',
      arrayCategoria : [],
      modal : 0,
      tituloModal : '',
      tipoAccion : 0,
      errorCategoria : 0,
      errorMostrarMsjCategoria : [],
      categoria_id : 0,
      pagination : {
        'total' : 0,
        'current_page' : 0,
        'per_page' : 0,
        'last_page' : 0,
        'from' : 0,
        'to' : 0,
      },
      offset : 3,
      criterio : 'nombre',
      buscar : ''
    };
  },
  computed : {
    isActived : function () {
      return this.pagination.current_page;
    },
    // Calcula los elementos de la paginación
    pagesNumber : function () {
      if (!this.pagination.to) {
        return [];
      }

      // Comprobamos que las páginas estan correctas
      var from = this.pagination.current_page - this.offset;
      if (from < 1) {
        from = 1;
      }


      var to = from + (this.offset * 2);
      if (to >= this.pagination.last_page) {
        to = this.pagination.last_page;
      }

      var pagesArray = [];
      while (from <= to) {
        pagesArray.push(from);
        from ++;
      }
      return pagesArray;
    }
  },
  methods : {
    listarCategoria(page, buscar, criterio) {
      // Make a request for a user with a given ID
      let me = this;
      var url = '/categoria?page=' + page + '&buscar=' + buscar + '&criterio=' + criterio;
      axios
        .get(url)
        .then(function(response) {
          var respuesta = response.data;
          me.arrayCategoria = respuesta.categorias.data;
          me.pagination = respuesta.pagination;
        })
        .catch(function(error) {
          // handle error
          console.log(error);
        });
    },
    cambiarPagina(page, buscar, criterio) {
      let me = this;
      // Actualiza la página actual
      me.pagination.current_page = page;
      // Envia la petición para visualizar la data de esa página
      me.listarCategoria(page, buscar, criterio);
    },
    registrarCategoria() {
      if (this.validarCategoria()) {
        return;
      }

      let me = this;

      // dos parámetros, primero la ruta y el segundo los valores que va a recibir el controlador
      axios
        .post("/categoria/registrar", {
          'nombre': this.nombre,
          'descripcion': this.descripcion
        })
        .then(function(response) {
          me.cerrarModal();
          me.listarCategoria(1, '', 'nombre');
        })
        .catch(function(error) {
          console.log(error);
        });
    },
    actualizarCategoria() {
      let me = this;

      axios
        .put("/categoria/actualizar", {
          id: this.categoria_id,
          nombre: this.nombre,
          descripcion: this.descripcion
        })
        .then(function(response) {
          me.cerrarModal();
          me.listarCategoria(1, '', 'nombre');
        })
        .catch(function(error) {
          console.log(error);
        });
    },
    activarCategoria(id) {
      const swalWithBootstrapButtons = Swal.mixin({
        confirmButtonClass: "btn btn-success",
        cancelButtonClass: "btn btn-danger",
        buttonsStyling: false
      });
      swalWithBootstrapButtons
        .fire({
          title: "¿Esta seguro de activar la categoría?",
          type: "warning",
          showCancelButton: true,
          confirmButtonText: "Aceptar",
          cancelButtonText: "Cancelar",
          reverseButtons: true
        })
        .then(result => {
          if (result.value) {
            let me = this;
            axios
              .put("/categoria/activar", {
                id: id
              })
              .then(function(response) {
                swalWithBootstrapButtons.fire(
                  "Activado",
                  "El registro ha sido activado",
                  "success"
                );
                me.listarCategoria(1, '', 'nombre');
              })
              .catch(function(error) {
                console.log(error.response);
              });
          }
        });
    },
    desactivarCategoria(id) {
      const swalWithBootstrapButtons = Swal.mixin({
        confirmButtonClass: "btn btn-success",
        cancelButtonClass: "btn btn-danger",
        buttonsStyling: false
      });
      swalWithBootstrapButtons
        .fire({
          title: "¿Esta seguro de desactivar la categoría?",
          type: "warning",
          showCancelButton: true,
          confirmButtonText: "Aceptar",
          cancelButtonText: "Cancelar",
          reverseButtons: true
        })
        .then(result => {
          if (result.value) {
            let me = this;
            axios
              .put("/categoria/desactivar", {
                id: id
              })
              .then(function(response) {
                swalWithBootstrapButtons.fire(
                  "Desactivado",
                  "El registro ha sido desativado",
                  "success"
                );
                me.listarCategoria(1, '', 'nombre');
              })
              .catch(function(error) {
                console.log(error.response);
              });
          }
        });
    },
    validarCategoria() {
      this.errorCategoria = 0;
      this.errorMostrarMsjCategoria = [];

      if (!this.nombre)
        this.errorMostrarMsjCategoria.push(
          "El nombre de la categoría no puede estar vacío"
        );

      if (this.errorMostrarMsjCategoria.length) this.errorCategoria = 1;

      return this.errorCategoria;
    },
    // Estas dos funcionalidades cerrarModel() y abrirModel() sirven para que cuando uno escoja la opción de 'nuevo'
    // para agregar una categoría aparezca una ventana emergente
    cerrarModal() {
      this.tipoAccion = 0;
      this.modal = 0;
      this.tituloModal = "";
      this.nombre = "";
      this.descripcion = "";
    },
    abrirModal(modelo, accion, data = []) {
      // nombre del modelo
      // accion será registrar o actualizar
      // data = [] objeto que se encuentra en esa fila
      switch (modelo) {
        case "categoria": {
          switch (accion) {
            case "registrar": {
              this.modal = 1;
              this.tituloModal = "Registrar Categoría";
              this.nombre = "";
              this.descripcion = "";
              this.tipoAccion = 1;
              break;
            }
            case "actualizar": {
              this.modal = 1;
              this.tituloModal = "Actualizar Categoría";
              this.nombre = data["nombre"];
              this.descripcion = data["descripcion"];
              this.tipoAccion = 2;
              this.categoria_id = data["id"];
              console.log(data);
              break;
            }
          }
        }
      }
    }
  },
  mounted() {
    this.listarCategoria(1, this.buscar, this.criterio);
  }
};
</script>

<style>
.modal-content {
  width: 100% !important;
  position: absolute !important;
}
.mostrar {
  display: list-item !important;
  opacity: 1 !important;
  position: absolute !important;
  background-color: #3c29297a !important;
}
.div-error {
  display: flex;
  justify-content: center;
}
.text-error {
  color: red !important;
  font-weight: bold;
}
</style>
