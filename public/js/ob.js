let ob = {
  operation: {
    renderPointe: function(data, _type, _row) {
      if (data) {
        return '<i class="uil uil-check"></i>';
      } else {
        return '';
      }
    }
  }
};
ob = {
    formatDate: 'd/m/Y',
    init: function () {
      // Event sur Banque
      document.querySelectorAll('button[id*="btn_mod_banque"]').forEach(function(btn) {
        btn.addEventListener('click', ob.banque.modForm, false);
      });
      document.querySelectorAll('button[id="btn_add_banque"]').forEach(function(btn) {
        btn.addEventListener('click', ob.banque.addForm, false);
      });

      // Event sur Categorie
      document.querySelectorAll('button[id*="btn_mod_categorie"]').forEach(function(btn) {
        btn.addEventListener('click', ob.categorie.modForm, false);
      });
      document.querySelectorAll('button[id="btn_add_categorie"]').forEach(function(btn) {
        btn.addEventListener('click', ob.categorie.addForm, false);
      });

      // Event sur Mode de paiement
      document.querySelectorAll('button[id*="btn_mod_modepaiement"]').forEach(function(btn) {
        btn.addEventListener('click', ob.modepaiement.modForm, false);
      });
      document.querySelectorAll('button[id="btn_add_modepaiement"]').forEach(function(btn) {
        btn.addEventListener('click', ob.modepaiement.addForm, false);
      });

      // Event sur Tiers
      document.querySelectorAll('button[id*="btn_mod_tiers"]').forEach(function(btn) {
        btn.addEventListener('click', ob.tiers.modForm, false);
      });
      document.querySelectorAll('button[id="btn_add_tiers"]').forEach(function(btn) {
        btn.addEventListener('click', ob.tiers.addForm, false);
      });

      // Event sur Compte
      document.querySelectorAll('button[id*="btn_mod_compte"]').forEach(function(btn) {
        btn.addEventListener('click', ob.compte.modForm, false);
      });
      document.querySelectorAll('button[id="btn_add_compte"]').forEach(function(btn) {
        btn.addEventListener('click', ob.compte.addForm, false);
      });
      document.querySelectorAll('a[id="btn_add_compte"]').forEach(function(btn) {
        btn.addEventListener('click', ob.compte.addForm, false);
      });

      // Event sur Operation
      document.querySelectorAll('form[id="operation_add_show"]').forEach(function(form) {
        form.addEventListener('submit', ob.operation.submitFormAdd, false);
      });
      document.querySelectorAll('button[id="btn_mod_operation"]').forEach(function(btn) {
        btn.addEventListener('click', ob.operation.submitFormMod, false);
      });

      // Event sur Ajax
      $(document).ajaxStop(function(){
        $("#ajax_loader").hide();
      });
      $(document).ajaxStart(function(){
        $("#ajax_loader").show();
      });
    },
    loadForm(idModal, url, fctAfterLoad, type='GET') {
      $.ajax({
        type: type,
        url: url,
        success: function(data) {
          $("#form").html(
            $.parseHTML(data)
          );
          let modal = new bootstrap.Modal(document.getElementById(idModal));
          modal.show();
          fctAfterLoad();
        }
      });
    },
    refreshCb: function(obj, idElement) {
      $.get('/'+ obj +'/select2', function(data) {
        let dataCb = [];
        data.data.forEach(function(element) {
          if (element.hasOwnProperty("actif")) {
            if(element.actif) {
              dataCb.push({
                id: element.id,
                text: element.libelle
              });
            }
          } else {
            dataCb.push({
              id: element.id,
              text: element.libelle
            });
          }
        });
        $('#'+idElement).select2({ data: dataCb, theme: 'bootstrap-5' });
      });
    },
    compte: {
      modName: 'modalFormCompte',
      tableName: 'table-compte',
      tableColumns: [
        { data: 'id', title: '#', visible: false, searchable: false },
        { data: 'date', title: 'Date', visible: true, searchable: true, className: 'dt-body-center', render: DataTable.render.date() },
        { data: 'libelle', title: 'Libellé', visible: true, searchable: true },
        { data: 'tiers', title: 'Tiers', visible: true, searchable: true },
        { data: 'modePaiement', title: 'Paiement', visible: true, searchable: true },
        { data: 'categorie', title: 'Catégorie', visible: true, searchable: true },
        { data: 'montant', title: 'Montant', visible: true, searchable: true, render: DataTable.render.number(' ', ',', 2, ''), className: 'dt-body-right' },
        { data: 'pointe', title: 'Pointé', visible: true, searchable: true, className: 'dt-body-center', render: ob.operation.renderPointe }
      ],
      modForm: function(evt) {
        ob.loadForm(
          ob.compte.modName,
          '/compte/modify/' + evt.target.dataset.compteid,
          ob.banque.compteBanque,
        );
      },
      addForm: function(_evt) {
        ob.loadForm(
          ob.compte.modName,
          '/compte/add',
          ob.banque.compteBanque,
        );
      },
      init: function() {
        ob.compte.initTable();
        ob.compte.initFlatpickr();
        ob.compte.initEvent();
      },
      initTable: function() {
        $('#'+ob.compte.tableName).DataTable({
          dom: 'Bfrtip',
          pagingType: "numbers",
          pageLength: 20,
          order: [[ 1, 'desc'], [7, 'asc']],
          fixedHeader: true,
          colReorder: true,
          columns: ob.compte.tableColumns,
          ajax: '/compte/' + document.getElementById('compte-id').value + '/table',
          dataSrc: "",
          autoWidth: true,
          select: {
            style: 'os'
          },
          buttons: [
            'copyHtml5', 'excelHtml5', 'pdfHtml5'
          ],
        });
      },

      refreshTable: function() {
        $('#'+ob.compte.tableName).DataTable().ajax.reload( null, false );
      },

      initFlatpickr: function() {
        flatpickr('#operation_form_date', {
          dateFormat: ob.formatDate
        });
      },

      initEvent: function() {
        $("#"+ob.compte.tableName).on('init', function() {
          $('#'+ob.compte.tableName).columns.adjust().draw();
        });

        $("#"+ob.compte.tableName).DataTable().on('select', function(_e, _dt, type, indexes) {
          if(type === 'row' && indexes.length == 1) { 
            let data = $("#"+ob.compte.tableName).DataTable().rows( indexes ).data()[0];
            $.ajax({
              type: 'GET',
              url: "/operation/" + data.id,
            }).done(function (json) {
              let o = json.data[0];
              document.getElementById('operation_form_date').value = o.date.split('-')[2] + '/' + o.date.split('-')[1] + '/' + o.date.split('-')[0];
              document.getElementById('operation_form_libelle').value = o.libelle;
              document.getElementById('operation_form_montant').value = o.montant;
              $('#btn_mod_operation').attr('data-operationid', o.id);
              $('#operation_form_modepaiement').val(o.modePaiementId).trigger('change');
              $('#operation_form_tiers').val(o.tiersId).trigger('change');
              $('#operation_form_categorie').val(o.categorieId).trigger('change');
              $('#operation_form_pointe').prop('checked', o.pointe);
            });
          }
        });

        document.addEventListener('DOMContentLoaded', function() {
            let cbs = document.querySelectorAll('[data-trigger]');
            for(let cb of cbs) {
              $(cb).select2({
                theme: 'bootstrap-5'
              });
            }
          }, false);

        document.addEventListener("keydown", function(event) {
          if (event.altKey && (event.key === 'p' || event.key === 'P'))
          {
              let operations=$('#'+ob.compte.tableName).DataTable().rows({ selected: true }).data();
              for(let o of operations) {
                ob.operation.pointe(o.id);
              }
              ob.compte.refreshTable();
          }
        }, false);
      },
    },
    operation: {
      pointe: function(id) {
        $.get('/operation/pointe/'+id);
      },
      delete: function(_evt) {
        Swal.fire({
          title: "Ëtes vous sur ?",
          text: "Vous ne pourrez pas faire marche arrière",
          icon: "warning",
          showCancelButton: true,
          confirmButtonColor: "#51d28c",
          cancelButtonColor: "#f34e4e",
          confirmButtonText: "Oui",
          cancelButtonText: "Non"
        }).then(function (result) {
          if (result.value) {
            Swal.fire("Deleted!", "Your file has been deleted.", "success"
            );
          }
        });
        $.get('/operation/pointe/'+id);
      },
      submitFormAdd: function(evt) {
        evt.preventDefault();
        $.ajax({
          type: evt.srcElement.method,
          url: evt.srcElement.action,
          data: $(this).serialize(),
        }).done(function (_data) {
          ob.compte.refreshTable();
        });
      },
      submitFormMod: function(evt) {
        evt.preventDefault();
        $.ajax({
          type: 'POST',
          url: '/operation/modify/{id}'.replace('{id}', evt.currentTarget.dataset.operationid),
          data: $("#operation_add_show").serialize(),
        }).done(function (_data) {
          ob.compte.refreshTable();
        });
      },
    },
    banque: {
      modName: 'modalFormBanque',
      modForm: function(evt) {
        ob.loadForm(
          ob.banque.modName,
          '/banque/modify/' + evt.target.dataset.banqueid
        );
      },
      addForm: function(_evt) {
        ob.loadForm(
          ob.banque.modName,
          '/banque/add'
        );
      },
      compteBanque: function() {
        document.querySelectorAll('button[id="btn_add_banque_compte"]').forEach(function(btn) {
          btn.addEventListener('click', ob.banque.addForm, false);
        });
      },
    },
    modepaiement: {
      modName: 'modalFormModePaiement',
      modForm: function(evt) {
        ob.loadForm(
          ob.modepaiement.modName,
          '/modepaiement/modify/' + evt.target.dataset.modepaiementid
        );
      },
      addForm: function(_evt) {
        ob.loadForm(
          ob.modepaiement.modName,
          '/modepaiement/add',
          () => {
            document.getElementById("mode_paiement_form").addEventListener('submit', ob.modepaiement.submitForm, false);
          }
        );
      },
      submitForm: function(evt) {
        evt.preventDefault();
        $.ajax({
          type: evt.srcElement.method,
          url: evt.srcElement.action,
          data: $(this).serialize(),
        }).done(function (_data) {
          ob.refreshCb("modepaiement", "operation_form_modepaiement");
          $('#'+ob.modepaiement.modName).modal('hide');
        });
      },
    },
    tiers: {
      modName: 'modalFormTiers',
      modForm: function(evt) {
        ob.loadForm(
          ob.tiers.modName,
          '/tiers/modify/' + evt.target.dataset.tiersid
        );
      },
      addForm: function(_evt) {
        ob.loadForm(
          ob.tiers.modName,
          '/tiers/add',
          () => {
            document.getElementById("tiers_form").addEventListener('submit', ob.tiers.submitForm, false);
          }
        );
      },
      submitForm: function(evt) {
        evt.preventDefault();
        $.ajax({
          type: evt.srcElement.method,
          url: evt.srcElement.action,
          data: $(this).serialize(),
        }).done(function (_data) {
          ob.refreshCb("tiers", "operation_form_tiers");
          $('#'+ob.tiers.modName).modal('hide');
        });
      },
    },
    categorie: {
      modName: 'modalFormCategorie',
      modForm: function(evt) {
        ob.loadForm(
          ob.categorie.modName,
          '/categorie/modify/' + evt.target.dataset.categorieid
        );
      },
      addForm: function(_evt) {
        ob.loadForm(
          ob.categorie.modName,
          '/categorie/add',
          () => {
            document.getElementById("categorie_form").addEventListener('submit', ob.categorie.submitForm, false);
          }
        );
      },
      submitForm: function(evt) {
        evt.preventDefault();
        $.ajax({
          type: evt.srcElement.method,
          url: evt.srcElement.action,
          data: $(this).serialize(),
        }).done(function (_data) {
          ob.refreshCb("categorie", "operation_form_categorie");
          $('#'+ob.categorie.modName).modal('hide');
        });
      },
    },
};

ob.init();