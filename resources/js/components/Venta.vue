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
          <i class="fa fa-align-justify"></i> Ventas
          <button type="button" @click="mostrarDetalle()" class="btn btn-secondary">
            <i class="icon-plus"></i>&nbsp;Nuevo
          </button>
        </div>
        <!-- Listado -->
        <template v-if="listado == 1">
          <div class="card-body">
            <div class="form-group row">
              <div class="col-md-6">
                <div class="input-group">
                  <select class="form-control col-md-3" v-model="criterio">
                    <option value="tipo_comprobante">Tipo comprobante</option>
                    <option value="num_comprobante">Número comprobante</option>
                    <option value="fecha_hora">Fecha-Hora</option>
                  </select>
                  <input
                    type="text"
                    class="form-control"
                    placeholder="Texto a buscar"
                    v-model="buscar"
                    @keyup.enter="listarVenta(1, buscar, criterio)"
                  >
                  <button
                    type="submit"
                    @click="listarVenta(1, buscar, criterio)"
                    class="btn btn-primary"
                  >
                    <i class="fa fa-search"></i> Buscar
                  </button>
                </div>
              </div>
            </div>
            <div class="table-responsive">
              <table class="table table-bordered table-striped table-sm">
                <thead>
                  <tr>
                    <th>Opciones</th>
                    <th>Usuario</th>
                    <th>Cliente</th>
                    <th>Tipo comprobante</th>
                    <th>Serie comprobante</th>
                    <th>Número comprobante</th>
                    <th>Fecha Hora</th>
                    <th>Impuesto</th>
                    <th>Total</th>
                    <th>Estado</th>
                  </tr>
                </thead>
                <tbody>
                  <!-- Creamos un for para listar las categorias es nuestra tabla -->
                  <tr v-for="venta in arrayVenta" :key="venta.id">
                    <td>
                      <button
                        type="button"
                        @click="verVenta(venta.id)"
                        class="btn btn-success btn-sm"
                      >
                        <i class="icon-eye"></i>
                      </button>&nbsp;
                      <template v-if="venta.estado == 'Registrado'">
                        <button
                          type="button"
                          @click="desactivarVenta(venta.id)"
                          class="btn btn-danger btn-sm"
                        >
                          <i class="icon-trash"></i>
                        </button>
                      </template>
                    </td>
                    <td v-text="venta.usuario"></td>
                    <td v-text="venta.nombre"></td>
                    <td v-text="venta.tipo_comprobante"></td>
                    <td v-text="venta.serie_comprobante"></td>
                    <td v-text="venta.num_comprobante"></td>
                    <td v-text="venta.fecha_hora"></td>
                    <td v-text="venta.impuesto"></td>
                    <td v-text="venta.total"></td>
                    <td v-text="venta.estado"></td>
                  </tr>
                </tbody>
              </table>
            </div>
            <nav>
              <ul class="pagination">
                <li class="page-item" v-if="pagination.current_page > 1">
                  <a
                    class="page-link"
                    @click.prevent="cambiarPagina(pagination.current_page - 1, buscar, criterio)"
                    href="#"
                  >Ant</a>
                </li>
                <li
                  class="page-item"
                  v-for="page in pagesNumber"
                  :key="page"
                  :class="[page == isActived ? 'active' : '']"
                >
                  <a
                    class="page-link"
                    href="#"
                    @click.prevent="cambiarPagina(page, buscar, criterio)"
                    v-text="page"
                  ></a>
                </li>
                <li class="page-item" v-if="pagination.current_page < pagination.last_page">
                  <a
                    class="page-link"
                    @click.prevent="cambiarPagina(pagination.current_page + 1, buscar, criterio)"
                    href="#"
                  >Sig</a>
                </li>
              </ul>
            </nav>
          </div>
        </template>
        <!-- Fin listado -->
        <!-- Detalles -->
        <template v-else-if="listado == 0">
          <div class="card-body">
            <div class="form-group row border">
              <div class="col-md-9">
                <div class="form-group">
                  <label for>Cliente(*)</label>
                  <!-- :on-search -> método para obtener los registros que voy a visualizar en la etiqueta -->
                  <!-- label -> el valor a mostrar -->
                  <!-- :options -> opciones que voy a mostrar, método para llenar el array -->
                  <!-- :onChange -> cada vez que cambiamos, podamos obtener los datos -->
                  <v-select
                    :on-search="selectCliente"
                    label="nombre"
                    :options="arrayCliente"
                    placeholder="Buscar clientes..."
                    :onChange="getDatosCliente"
                  ></v-select>
                </div>
              </div>
              <div class="col-md-3">
                <label for>Impuesto(*)</label>
                <input type="text" class="form-control" v-model="impuesto">
              </div>
              <div class="col-md-4">
                <div class="form-group">
                  <label for>Tipo Comprobante(*)</label>
                  <select class="form-control" v-model="tipo_comprobante">
                    <option value="0">Seleccione</option>
                    <option value="BOLETA">Boleta</option>
                    <option value="FACTURA">Factura</option>
                    <option value="TICKET">Ticket</option>
                  </select>
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                  <label for>Serie Comprobante</label>
                  <input
                    type="text"
                    class="form-control"
                    v-model="serie_comprobante"
                    placeholder="000x"
                  >
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                  <label for>Número Comprobante(*)</label>
                  <input
                    type="text"
                    class="form-control"
                    v-model="num_comprobante"
                    placeholder="000x"
                  >
                </div>
              </div>
              <div class="col-md-12">
                <div v-show="errorVenta" class="form-group row div-error">
                  <div class="text-center text-error">
                    <div v-for="error in errorMostrarMsjVenta" :key="error" v-text="error"></div>
                  </div>
                </div>
              </div>
            </div>
            <div class="form-group row border">
              <div class="col-md-4">
                <div class="form-group">
                  <label for>
                    Artículo
                    <span class="text-danger" v-show="idarticulo == 0">(*Seleccione)</span>
                  </label>
                  <div class="form-inline">
                    <input
                      type="text"
                      class="form-control"
                      v-model="codigo"
                      @keyup.enter="buscarArticulo()"
                      placeholder="Ingrese artículo"
                    >
                    <button class="btn btn-primary" @click="buscarArticulo()">
                      <i class="icon-plus"></i>
                    </button>
                    <button class="btn btn-primary" @click="abrirModal()">...</button>
                    <input type="text" readonly class="form-control" v-model="articulo">
                  </div>
                </div>
              </div>
              <div class="col-md-2">
                <div class="form-group">
                  <label for>
                    Precio
                    <span class="text-danger" v-show="precio == 0">(*Ingrese)</span>
                  </label>
                  <input
                    type="number"
                    step="any"
                    class="form-control"
                    min="0"
                    value="0"
                    v-model="precio"
                  >
                </div>
              </div>
              <div class="col-md-2">
                <div class="form-group">
                  <label for>
                    Cantidad
                    <span class="text-danger" v-show="cantidad == 0">(*Ingrese)</span>
                  </label>
                  <input type="number" class="form-control" value="0" min="0" v-model="cantidad">
                </div>
              </div>
              <div class="col-md-2">
                <div class="form-group">
                  <label for>Descuento</label>
                  <input type="number" class="form-control" value="0" min="0" v-model="descuento">
                </div>
              </div>
              <div class="col-md-2">
                <div class="form-group">
                  <button class="btn btn-success form-control btnagregar" @click="agregarDetalle()">
                    <i class="icon-plus"></i>
                  </button>
                </div>
              </div>
            </div>

            <!-- Lista artículos agregados -->
            <div class="form-group row border">
              <div class="table-responsive col-md-12">
                <table class="table table-bordered table-striped table-sm">
                  <thead>
                    <tr>
                      <th>Opciones</th>
                      <th>Artículos</th>
                      <th>Precio</th>
                      <th>Cantidad</th>
                      <th>Descuento</th>
                      <th>Subtotal</th>
                    </tr>
                  </thead>
                  <tbody v-if="arrayDetalle.length">
                    <tr v-for="(detalle, index) in arrayDetalle" :key="detalle.id">
                      <td>
                        <button
                          @click="eliminarDetalle(index)"
                          type="button"
                          class="btn btn-danger btn-sm"
                        >
                          <i class="icon-close"></i>
                        </button>
                      </td>
                      <td v-text="detalle.articulo"></td>
                      <td>
                        <input
                          type="number"
                          v-model="detalle.precio"
                          step="any"
                          min="0"
                          class="form-control"
                        >
                      </td>
                      <td>
                        <input
                          type="number"
                          v-model="detalle.cantidad"
                          step="any"
                          min="0"
                          class="form-control"
                        >
                      </td>
                      <td>
                        <input
                          type="number"
                          v-model="detalle.descuento"
                          step="any"
                          min="0"
                          class="form-control"
                        >
                      </td>
                      <td>{{ detalle.precio*detalle.cantidad }}</td>
                    </tr>
                    <!--  -->
                    <tr class="fondo-tabla">
                      <td colspan="5" align="right">
                        <strong>Total Parcial:</strong>
                      </td>
                      <td>$ {{ totalParcial = total - totalImpuesto }}</td>
                    </tr>
                    <tr class="fondo-tabla">
                      <td colspan="5" align="right">
                        <strong>Total Impuesto:</strong>
                      </td>
                      <td>$ {{ totalImpuesto = ((total * impuesto) / (1 + impuesto)).toFixed(2) }}</td>
                    </tr>
                    <tr class="fondo-tabla">
                      <td colspan="5" align="right">
                        <strong>Total Neto:</strong>
                      </td>
                      <td>$ {{ total = calcularTotal }}</td>
                    </tr>
                  </tbody>
                  <tbody v-else>
                    <tr>
                      <td colspan="6">No hay artículos agregados</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
            <div class="form-group row">
              <div class="col-md-12">
                <button type="button" class="btn btn-secondary" @click="ocultarDetalle()">Cerrar</button>
                <button
                  type="button"
                  class="btn btn-primary"
                  @click="registrarVenta()"
                >Registrar venta</button>
              </div>
            </div>
          </div>
        </template>
        <!-- Fin detalles -->
        <!-- Ver ingreso -->
        <template v-else-if="listado == 2">
          <div class="card-body">
            <div class="form-group row border">
              <div class="col-md-9">
                <div class="form-group">
                  <label for>Proveedor</label>
                  <p v-text="proveedor"></p>
                </div>
              </div>
              <div class="col-md-3">
                <label for>Impuesto</label>
                <p v-text="impuesto"></p>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                  <label for>Tipo Comprobante</label>
                  <p v-text="tipo_comprobante"></p>
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                  <label for>Serie Comprobante</label>
                  <p v-text="serie_comprobante"></p>
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                  <label for>Número Comprobante</label>
                  <p v-text="num_comprobante"></p>
                </div>
              </div>
            </div>

            <!-- Lista artículos agregados -->
            <div class="form-group row border">
              <div class="table-responsive col-md-12">
                <table class="table table-bordered table-striped table-sm">
                  <thead>
                    <tr>
                      <th>Artículos</th>
                      <th>Precio</th>
                      <th>Cantidad</th>
                      <th>Subtotal</th>
                    </tr>
                  </thead>
                  <tbody v-if="arrayDetalle.length">
                    <tr v-for="detalle in arrayDetalle" :key="detalle.id">
                      <td v-text="detalle.articulo"></td>
                      <td v-text="detalle.precio"></td>
                      <td v-text="detalle.cantidad"></td>
                      <td>{{ detalle.precio*detalle.cantidad }}</td>
                    </tr>
                    <!--  -->
                    <tr class="fondo-tabla">
                      <td colspan="5" align="right">
                        <strong>Total Parcial:</strong>
                      </td>
                      <td>$ {{ totalParcial = total - totalImpuesto }}</td>
                    </tr>
                    <tr class="fondo-tabla">
                      <td colspan="5" align="right">
                        <strong>Total Impuesto:</strong>
                      </td>
                      <td>$ {{ totalImpuesto = (total * impuesto) }}</td>
                    </tr>
                    <tr class="fondo-tabla">
                      <td colspan="5" align="right">
                        <strong>Total Neto:</strong>
                      </td>
                      <td>$ {{ total }}</td>
                    </tr>
                  </tbody>
                  <tbody v-else>
                    <tr>
                      <td colspan="6">No hay artículos agregados</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
            <div class="form-group row">
              <div class="col-md-12">
                <button type="button" class="btn btn-secondary" @click="ocultarDetalle()">Cerrar</button>
              </div>
            </div>
          </div>
        </template>
        <!-- fin ver ingreso -->
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
            <!-- Modal para seleccionar articulos -->
            <!-- Buscador -->
            <div class="form-group row">
              <div class="col-md-6">
                <div class="input-group">
                  <select class="form-control col-md-3" v-model="criterioA">
                    <option value="nombre">Nombre</option>
                    <option value="descripcion">Descripción</option>
                    <option value="codigo">Código</option>
                  </select>
                  <input
                    type="text"
                    class="form-control"
                    placeholder="Texto a buscar"
                    v-model="buscarA"
                    @keyup.enter="listarArticulo (buscarA, criterioA)"
                  >
                  <button
                    type="submit"
                    @click="listarArticulo (buscarA, criterioA)"
                    class="btn btn-primary"
                  >
                    <i class="fa fa-search"></i> Buscar
                  </button>
                </div>
              </div>
            </div>
            <!-- Todos los artículos -->
            <div class="table-responsive">
              <table class="table table-bordered table-striped table-sm">
                <thead>
                  <tr>
                    <th>Opciones</th>
                    <th>Codigo</th>
                    <th>Nombre</th>
                    <th>Categoría</th>
                    <th>Precio venta</th>
                    <th>Stock</th>
                    <th>Estado</th>
                  </tr>
                </thead>
                <tbody>
                  <!-- Creamos un for para listar las articulos es nuestra tabla -->
                  <tr v-for="articulo in arrayArticulo" :key="articulo.id">
                    <td>
                      <button
                        type="button"
                        @click="agregarDetalleModal(articulo)"
                        class="btn btn-sucess btn-sm"
                      >
                        <i class="icon-check"></i>
                      </button>
                    </td>
                    <td v-text="articulo.codigo"></td>
                    <td v-text="articulo.nombre"></td>
                    <td v-text="articulo.nombre_categoria"></td>
                    <td v-text="articulo.precio_venta"></td>
                    <td v-text="articulo.stock"></td>
                    <td>
                      <div v-if="articulo.condicion">
                        <span class="badge badge-success">Activo</span>
                      </div>
                      <div v-else>
                        <span class="badge badge-danger">Descativado</span>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" @click="cerrarModal()" class="btn btn-secondary">Cerrar</button>
            <button
              type="button"
              v-if="tipoAccion==1"
              @click="registrarVenta()"
              class="btn btn-primary"
            >Guardar</button>
            <button
              type="button"
              v-if="tipoAccion==2"
              @click="actualizarPersona()"
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
// importo el componente vue select
import vSelect from "vue-select";
export default {
  // declaramos el data para guardar la información recibida
  data() {
    return {
      venta_id: 0,
      idcliente: 0,
      tipo_comprobante: "BOLETA",
      serie_comprobante: "",
      num_comprobante: "",
      impuesto: 0.18,
      total: 0.0,
      totalImpuesto: 0.0,
      totalParcial: 0.0,
      arrayVenta: [],
      arrayDetalle: [],
      arrayCliente: [],
      arrayArticulo: [],
      idarticulo: 0,
      codigo: "",
      articulo: "",
      precio: 0,
      cantidad: 0,
      descuento: 0,
      // Controlo si visualizo el listado o no, 1 listado detalles
      listado: 1,

      // listar artículos ventana modal
      criterioA: "nombre",
      buscarA: "",

      // Info clientes
      proveedor: "",

      modal: 0,
      tituloModal: "",
      tipoAccion: 0,
      errorVenta: 0,
      errorMostrarMsjVenta: [],
      pagination: {
        total: 0,
        current_page: 0,
        per_page: 0,
        last_page: 0,
        from: 0,
        to: 0
      },
      offset: 3,
      criterio: "num_comprobante",
      buscar: ""
    };
  },
  // añado esto despues de importar vue-select
  components: {
    vSelect
  },
  computed: {
    isActived: function() {
      return this.pagination.current_page;
    },
    // Calcula los elementos de la paginación
    pagesNumber: function() {
      if (!this.pagination.to) {
        return [];
      }

      // Comprobamos que las páginas estan correctas
      var from = this.pagination.current_page - this.offset;
      if (from < 1) {
        from = 1;
      }

      var to = from + this.offset * 2;
      if (to >= this.pagination.last_page) {
        to = this.pagination.last_page;
      }

      var pagesArray = [];
      while (from <= to) {
        pagesArray.push(from);
        from++;
      }
      return pagesArray;
    },
    calcularTotal: function() {
      var resultado = 0.0;

      for (var i = 0; i < this.arrayDetalle.length; i++) {
        resultado +=
          this.arrayDetalle[i].precio * this.arrayDetalle[i].cantidad;
      }

      return resultado;
    }
  },
  methods: {
    listarVenta(page, buscar, criterio) {
      // Make a request for a user with a given ID
      let me = this;
      var url =
        "/venta?page=" + page + "&buscar=" + buscar + "&criterio=" + criterio;
      axios
        .get(url)
        .then(function(response) {
          var respuesta = response.data;
          me.arrayVenta = respuesta.ventas.data;
          me.pagination = respuesta.pagination;
        })
        .catch(function(error) {
          // handle error
          console.log(error);
        });
    },
    selectCliente(search, loading) {
      let me = this;
      loading(true);

      var url = "/cliente/selectCliente?filtro=" + search;
      axios
        .get(url)
        .then(function(response) {
          var respuesta = response.data;
          q: search;
          me.arrayCliente = respuesta.clientes;
          loading(false);
        })
        .catch(function(error) {
          console.log(error);
        });
    },
    // val1 = opcion seleccionada
    getDatosCliente(val1) {
      let me = this;
      me.loading = true;
      // toda la opcion del proveedor seleccionado
      me.idcliente = val1.id;
    },
    buscarArticulo() {
      let me = this;
      var url = "/articulo/buscarArticulo?filtro=" + me.codigo;

      axios
        .get(url)
        .then(function(response) {
          var respuesta = response.data;
          me.arrayArticulo = respuesta.articulos;

          if (me.arrayArticulo.length > 0) {
            me.articulo = me.arrayArticulo[0]["nombre"];
            me.idarticulo = me.arrayArticulo[0]["id"];
          } else {
            me.articulo = "No existe el artículo";
            me.idarticulo = 0;
          }
        })
        .catch(function(error) {
          console.log(error);
        });
    },
    selectRol() {
      // Make a request for a user with a given ID
      let me = this;
      var url = "/rol/selectRol";
      axios
        .get(url)
        .then(function(response) {
          var respuesta = response.data;
          me.arrayRol = respuesta.roles;
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
      me.listarVenta(page, buscar, criterio);
    },
    // Compruebo si ya esta el artículo agregado
    encuentra(id) {
      var sw = 0;
      for (var i = 0; i < this.arrayDetalle.length; i++) {
        if (this.arrayDetalle[i].idarticulo == id) {
          sw = true;
        }
      }
      return sw;
    },
    agregarDetalle() {
      let me = this;

      //
      const swalWithBootstrapButtons = Swal.mixin({
        confirmButtonClass: "btn btn-success",
        cancelButtonClass: "btn btn-danger",
        buttonsStyling: false
      });
      //

      // Valido si los datos están bien introducidos
      if (me.idarticulo == 0 || me.cantidad == 0 || me.precio == 0) {
        // NO HAGO NADA
      } else {
        // Verifico si un artículo esta agregado
        if (me.encuentra(me.idarticulo)) {
          swalWithBootstrapButtons.fire({
            title: "Error...",
            type: "error",
            confirmButtonText: "Aceptar",
            reverseButtons: true,
            text: "Ese artículo ya se encuentra agregado"
          });
          //
        } else {
          // Inserto un artículo
          me.arrayDetalle.push({
            idarticulo: me.idarticulo,
            articulo: me.articulo,
            cantidad: me.cantidad,
            precio: me.precio
          });
          // Vuelvo a inicializar los datos iniciales al insertar un artículo
          me.codigo = "";
          me.idarticulo = 0;
          me.cantidad = 0;
          me.precio = 0;
          me.articulo = "";
        }
      }
    },
    agregarDetalleModal(data = []) {
      let me = this;
      const swalWithBootstrapButtons = Swal.mixin({
        confirmButtonClass: "btn btn-success",
        cancelButtonClass: "btn btn-danger",
        buttonsStyling: false
      });
      // Verifico si un artículo esta agregado
      if (me.encuentra(data["id"])) {
        swalWithBootstrapButtons.fire({
          title: "Error...",
          type: "error",
          confirmButtonText: "Aceptar",
          reverseButtons: true,
          text: "Ese artículo ya se encuentra agregado"
        });
        //
      } else {
        // Inserto un artículo
        me.arrayDetalle.push({
          idarticulo: data["id"],
          articulo: data["nombre"],
          cantidad: 1,
          precio: 1
        });
      }
    },
    listarArticulo(buscar, criterio) {
      // Make a request for a user with a given ID
      let me = this;
      var url =
        "/articulo/listarArticulo?buscar=" + buscar + "&criterio=" + criterio;
      axios
        .get(url)
        .then(function(response) {
          var respuesta = response.data;
          me.arrayArticulo = respuesta.articulos.data;
        })
        .catch(function(error) {
          // handle error
          console.log(error);
        });
    },
    eliminarDetalle(index) {
      let me = this;
      me.arrayDetalle.splice(index, 1);
    },
    registrarVenta() {
      if (this.validarIngreso()) {
        return;
      }

      let me = this;

      // dos parámetros, primero la ruta y el segundo los valores que va a recibir el controlador
      axios
        .post("/ingreso/registrar", {
          idproveedor: me.idproveedor,
          tipo_comprobante: me.tipo_comprobante,
          serie_comprobante: me.serie_comprobante,
          num_comprobante: me.num_comprobante,
          impuesto: me.impuesto,
          total: me.total,
          data: me.arrayDetalle
        })
        .then(function(response) {
          me.listado = 1;
          me.listarVenta(1, "", "num_comprobante");
          me.idproveedor = 0;
          me.tipo_comprobante = "BOLETA";
          me.serie_comprobante = "";
          me.num_comprobante = "";
          me.impuesto = 0.18;
          me.total = 0.0;
          me.idarticulo = 0;
          me.articulo = "";
          me.cantidad = 0;
          me.precio = 0;
          me.arrayDetalle = [];
        })
        .catch(function(error) {
          console.log(error);
        });
    },
    validarIngreso() {
      this.errorVenta = 0;
      this.errorMostrarMsjVenta = [];

      if (!this.idproveedor) this.errorMostrarMsjVenta.push("Proveedor vacío");

      if (!this.tipo_comprobante) {
        this.errorMostrarMsjVenta.push("Seleccione el comprobante");
      }

      if (!this.num_comprobante) {
        this.errorMostrarMsjVenta.push("Ingrese el número de comprobante");
      }

      if (!this.impuesto) {
        this.errorMostrarMsjVenta.push("Ingrese el impuesto");
      }

      if (this.arrayDetalle <= 0) {
        this.errorMostrarMsjVenta.push("Ingrese detalles");
      }

      if (this.errorMostrarMsjVenta.length) this.errorVenta = 1;

      return this.errorVenta;
    },
    mostrarDetalle() {
      let me = this;
      me.listado = 0;

      me.idproveedor = 0;
      me.tipo_comprobante = "BOLETA";
      me.serie_comprobante = "";
      me.num_comprobante = "";
      me.impuesto = 0.18;
      me.total = 0.0;
      me.idarticulo = 0;
      me.articulo = "";
      me.cantidad = 0;
      me.precio = 0;
      me.arrayDetalle = [];
    },
    ocultarDetalle() {
      this.listado = 1;
    },
    verIngreso(id) {
      let me = this;
      me.listado = 2;

      // Obtener los datos del ingreso
      var arrayIngresoT = [];
      var url = "/ingreso/obtenerCabecera?id=" + id;
      axios
        .get(url)
        .then(function(response) {
          var respuesta = response.data;
          arrayIngresoT = respuesta.ingreso;

          me.proveedor = arrayIngresoT[0]["nombre"];
          me.tipo_comprobante = arrayIngresoT[0]["tipo_comprobante"];
          me.serie_comprobante = arrayIngresoT[0]["serie_comprobante"];
          me.num_comprobante = arrayIngresoT[0]["num_comprobante"];
          me.impuesto = arrayIngresoT[0]["impuesto"];
          me.total = arrayIngresoT[0]["total"];
        })
        .catch(function(error) {
          console.log(error);
        });

      // Obtener los datos del detalle
      var urld = "/ingreso/obtenerDetalles?id=" + id;
      axios
        .get(urld)
        .then(function(response) {
          var respuesta = response.data;
          me.arrayDetalle = respuesta.detalles;
        })
        .catch(function(error) {
          console.log(error);
        });
    },
    // Estas dos funcionalidades cerrarModel() y abrirModel() sirven para que cuando uno escoja la opción de 'nuevo'
    // para agregar una categoría aparezca una ventana emergente
    cerrarModal() {
      this.modal = 0;
      this.tituloModal = "";
    },
    abrirModal() {
      this.arrayArticulo = [];
      this.modal = 1;
      this.tituloModal = "Seleccione uno o varios artículos";
    },
    desactivarVenta(id) {
      const swalWithBootstrapButtons = Swal.mixin({
        confirmButtonClass: "btn btn-success",
        cancelButtonClass: "btn btn-danger",
        buttonsStyling: false
      });
      swalWithBootstrapButtons
        .fire({
          title: "¿Esta seguro de anular este ingreso?",
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
              .put("/ingreso/desactivar", {
                id: id
              })
              .then(function(response) {
                swalWithBootstrapButtons.fire(
                  "Anulado",
                  "El ingreso ha sido anulado",
                  "success"
                );
                me.listarVenta(1, "", "num_comprobante");
              })
              .catch(function(error) {
                console.log(error.response);
              });
          }
        });
    }
  },
  mounted() {
    this.listarVenta(1, this.buscar, this.criterio);
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

@media (min-width: 600px) {
  .btnagregar {
    margin-top: 2rem;
  }
}

/* Quitar una pequeña flechita que aparecia en el input del formulario */
.dropdown-toggle::after {
  content: none;
}

.fondo-tabla {
  background-color: #ceecf5 !important;
}
</style>
