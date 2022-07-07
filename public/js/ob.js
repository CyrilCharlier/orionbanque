let ob = {
  operation: {
    renderPointe: function(data, type, row) {
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
    byId: function (id) {
      return document.getElementById(id);
    },
    queryAll: function (str) {
      return document.querySelectorAll(str);
    },
    addEvent: function(element, event, func) {
      element.addEventListener(event, func, false);
    },
    init: function () {
      // Event sur Banque
      ob.queryAll('button[id*="btn_mod_banque"]').forEach(function(btn) {
        ob.addEvent(btn, 'click', ob.banque.modForm);
      });
      ob.queryAll('button[id="btn_add_banque"]').forEach(function(btn) {
        ob.addEvent(btn, 'click', ob.banque.addForm);
      });
      // Event sur Categorie
      ob.queryAll('button[id*="btn_mod_categorie"]').forEach(function(btn) {
        ob.addEvent(btn, 'click', ob.categorie.modForm);
      });
      ob.queryAll('button[id="btn_add_categorie"]').forEach(function(btn) {
        ob.addEvent(btn, 'click', ob.categorie.addForm);
      });

      // Event sur Mode de paiement
      ob.queryAll('button[id*="btn_mod_modepaiement"]').forEach(function(btn) {
        ob.addEvent(btn, 'click', ob.modepaiement.modForm);
      });
      ob.queryAll('button[id="btn_add_modepaiement"]').forEach(function(btn) {
        ob.addEvent(btn, 'click', ob.modepaiement.addForm);
      });

      // Event sur Tiers
      ob.queryAll('button[id*="btn_mod_tiers"]').forEach(function(btn) {
        ob.addEvent(btn, 'click', ob.tiers.modForm);
      });
      ob.queryAll('button[id="btn_add_tiers"]').forEach(function(btn) {
        ob.addEvent(btn, 'click', ob.tiers.addForm);
      });

      // Event sur Compte
      ob.queryAll('button[id*="btn_mod_compte"]').forEach(function(btn) {
        ob.addEvent(btn, 'click', ob.compte.modForm);
      });
      ob.queryAll('button[id="btn_add_compte"]').forEach(function(btn) {
        ob.addEvent(btn, 'click', ob.compte.addForm);
      });
      ob.queryAll('a[id="btn_add_compte"]').forEach(function(btn) {
        ob.addEvent(btn, 'click', ob.compte.addForm);
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
          let modal = new bootstrap.Modal(ob.byId(idModal));
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
      addForm: function(evt) {
        ob.loadForm(
          ob.compte.modName,
          '/compte/add',
          function() {
            ob.queryAll('button[id="btn_add_banque_compte"]').forEach(function(btn) {
              ob.addEvent(btn, 'click', ob.banque.addForm);
            });
          },
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
          order: [[ 1, 'desc' ]],
          fixedHeader: true,
          colReorder: true,
          columns: ob.compte.tableColumns,
          ajax: '/compte/' + ob.byId('compte-id').value + '/table',
          dataSrc: 'data',
          autoWidth: true,
          select: {
            style: 'multi'
          },
          buttons: [
            'copyHtml5', 'excelHtml5', 'pdfHtml5'
          ],
        });
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

        ob.addEvent(document, 'DOMContentLoaded', function() {
          var cb = ob.queryAll('[data-trigger]');
          for (i = 0; i < cb.length; ++i) {
            var element = cb[i];
            $(element).select2({
              theme: 'bootstrap-5'
            });
          }
        });

        document.addEventListener("keydown", function(event) {
          if (event.altKey && (event.key === 'p' || event.key === 'P'))
          {
              let data=$('#'+ob.compte.tableName).DataTable().rows({ selected: true }).data();
              for(i=0;i<data.length;i++)
              {
                console.log(data[i]);
                ob.operation.pointe(data[i].id);
              }
              $('#'+ob.compte.tableName).DataTable().ajax.reload( null, false );
          }
        });
      },
    },
    operation: {
      pointe: function(id) {
        $.get('/operation/pointe/'+id, function(data) {

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
      addForm: function(evt) {
        ob.loadForm(
          ob.banque.modName,
          '/banque/add'
        );
      },
      compteBanque: function() {
        ob.queryAll('button[id="btn_add_banque_compte"]').forEach(function(btn) {
          ob.addEvent(btn, 'click', ob.banque.addForm);
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
      addForm: function(evt) {
        ob.loadForm(
          ob.modepaiement.modName,
          '/modepaiement/add',
          () => {
            ob.addEvent(ob.byId("mode_paiement_form"), "submit", ob.modepaiement.submitForm);
          }
        );
      },
      submitForm: function(evt) {
        evt.preventDefault();
        $.ajax({
          type: evt.srcElement.method,
          url: evt.srcElement.action,
          data: $(this).serialize(),
        }).done(function (data) {
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
      addForm: function(evt) {
        ob.loadForm(
          ob.tiers.modName,
          '/tiers/add',
          () => {
            ob.addEvent(ob.byId("tiers_form"), "submit", ob.tiers.submitForm);
          }
        );
      },
      submitForm: function(evt) {
        evt.preventDefault();
        $.ajax({
          type: evt.srcElement.method,
          url: evt.srcElement.action,
          data: $(this).serialize(),
        }).done(function (data) {
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
      addForm: function(evt) {
        ob.loadForm(
          ob.categorie.modName,
          '/categorie/add',
          () => {
            ob.addEvent(ob.byId("categorie_form"), "submit", ob.categorie.submitForm);
          }
        );
      },
      submitForm: function(evt) {
        evt.preventDefault();
        $.ajax({
          type: evt.srcElement.method,
          url: evt.srcElement.action,
          data: $(this).serialize(),
        }).done(function (data) {
          ob.refreshCb("categorie", "operation_form_categorie");
          $('#'+ob.categorie.modName).modal('hide');
        });
      },
    },
};

ob.init();