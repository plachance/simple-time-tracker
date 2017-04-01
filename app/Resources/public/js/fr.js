$.fn.select2.defaults.set("language", "fr");
$.extend($.fn.dataTable.defaults, {
	language: {
		processing: "Traitement en cours...",
		search: "Rechercher&nbsp;:",
		lengthMenu: "Afficher _MENU_ &eacute;l&eacute;ments",
		info: "Affichage de l'&eacute;l&eacute;ment _START_ &agrave; _END_ sur _TOTAL_ &eacute;l&eacute;ments",
		infoEmpty: "Affichage de l'&eacute;l&eacute;ment 0 &agrave; 0 sur 0 &eacute;l&eacute;ment",
		infoFiltered: "(filtr&eacute; de _MAX_ &eacute;l&eacute;ments au total)",
		infoPostFix: "",
		loadingRecords: "Chargement en cours...",
		zeroRecords: "Aucun &eacute;l&eacute;ment &agrave; afficher",
		emptyTable: "Aucune donn&eacute;e disponible dans le tableau",
		paginate: {
			first: "Premier",
			previous: "Pr&eacute;c&eacute;dent",
			next: "Suivant",
			last: "Dernier"
		},
		aria: {
			sortAscending: ": activer pour trier la colonne par ordre croissant",
			sortDescending: ": activer pour trier la colonne par ordre d&eacute;croissant"
		}
	}
});