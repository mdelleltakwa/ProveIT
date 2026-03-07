<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Inscription – ProveIt</title>

<link rel="icon" type="image/png" href="public/images/logo.png">
<link rel="stylesheet" href="public/css/app.css">
<link href="https://cdn.jsdelivr.net/npm/@mdi/font@7.0.96/css/materialdesignicons.min.css" rel="stylesheet">

</head>

<body>

<div class="pi-signup-wrapper">
<div class="pi-signup-card animate-in">

<div class="logo">
<img src="public/images/icon.png" width="150">
</div>

<h3>Créer un compte</h3>

<?php if (isset($error)): ?>
<div class="pi-alert pi-alert-error"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>


<form method="POST" id="registerForm">

<?= csrf_field() ?>


<!-- ROLE -->
<div class="pi-form-group" id="roleSelection">

<label>Je suis</label>

<div class="pi-role-select">

<label class="pi-role-option" id="role-candidat-label">
<input type="radio" name="role" value="candidat">

<div class="pi-role-card">
<span class="pi-role-icon"><i class="mdi mdi-laptop"></i></span>
<strong>Candidat</strong>
<span class="pi-role-desc">Participer aux hackathons</span>
</div>
</label>


<label class="pi-role-option" id="role-orga-label">
<input type="radio" name="role" value="organisateur">

<div class="pi-role-card">
<span class="pi-role-icon"><i class="mdi mdi-target"></i></span>
<strong>Organisateur</strong>
<span class="pi-role-desc">Créer des hackathons</span>
</div>
</label>

</div>
</div>



<!-- CANDIDAT -->
<div id="candidatForm" style="display:none;">

<div class="pi-form-group">
<label>Nom</label>
<input type="text" id="candidatName" name="name" class="pi-input">
</div>

<div class="pi-form-group">
<label>Email</label>
<input type="email" id="candidatEmail" name="email" class="pi-input">
</div>

<div class="pi-form-group">
<label>Mot de passe</label>
<input type="password" id="candidatPassword" name="password" class="pi-input">
</div>

<div class="pi-form-group">
<label>Confirmer le mot de passe</label>
<input type="password" id="candidatConfirm" name="confirm_password" class="pi-input">

<div id="errorCand" class="pi-alert pi-alert-error" style="display:none;margin-top:5px;">
Les mots de passe ne correspondent pas
</div>

</div>

<button type="submit" class="pi-btn pi-btn-primary pi-btn-block pi-btn-lg mb-3">
Créer le compte Candidat
</button>

<button type="button" class="pi-btn pi-btn-secondary pi-btn-block" id="backFromCandidat">
← Retour
</button>

</div>



<!-- ORGANISATEUR -->
<div id="orgaForm" style="display:none;">

<div class="pi-form-group">
<label>Nom</label>
<input type="text" id="orgaName" name="name" class="pi-input">
</div>

<div class="pi-form-group">
<label>Email</label>
<input type="email" id="orgaEmail" name="email" class="pi-input">
</div>

<div class="pi-form-group">
<label>Mot de passe</label>
<input type="password" id="orgaPassword" name="password" class="pi-input">
</div>

<div class="pi-form-group">
<label>Confirmer le mot de passe</label>
<input type="password" id="orgaConfirm" name="confirm_password" class="pi-input">

<div id="errorOrga" class="pi-alert pi-alert-error" style="display:none;margin-top:5px;">
Les mots de passe ne correspondent pas
</div>

</div>

<button type="submit" class="pi-btn pi-btn-primary pi-btn-block pi-btn-lg mb-3">
Créer le compte Organisateur
</button>

<button type="button" class="pi-btn pi-btn-secondary pi-btn-block" id="backFromOrga">
← Retour
</button>

</div>

</form>



<div class="pi-signup-footer">
Déjà un compte ?
<a href="index.php?controller=User&action=login">Se connecter</a>
</div>

</div>
</div>



<script>

/* ELEMENTS */

const roleCandidat = document.getElementById('role-candidat-label');
const roleOrga = document.getElementById('role-orga-label');

const roleSelection = document.getElementById('roleSelection');

const candidatForm = document.getElementById('candidatForm');
const orgaForm = document.getElementById('orgaForm');

const backFromCandidat = document.getElementById('backFromCandidat');
const backFromOrga = document.getElementById('backFromOrga');

const form = document.getElementById('registerForm');


/* ENABLE / DISABLE INPUTS */

function toggleInputs(form,state){

const inputs=form.querySelectorAll("input");

inputs.forEach(input=>{
if(input.type!=="radio"){
input.disabled=!state;
input.required=state;
}
});

}


/* SHOW CANDIDAT */

roleCandidat.onclick = () => {

roleSelection.style.display="none";
candidatForm.style.display="block";

toggleInputs(candidatForm,true);
toggleInputs(orgaForm,false);

};


/* SHOW ORGA */

roleOrga.onclick = () => {

roleSelection.style.display="none";
orgaForm.style.display="block";

toggleInputs(orgaForm,true);
toggleInputs(candidatForm,false);

};


/* BACK */

backFromCandidat.onclick = () => {

candidatForm.style.display="none";
roleSelection.style.display="block";

};

backFromOrga.onclick = () => {

orgaForm.style.display="none";
roleSelection.style.display="block";

};



/* PASSWORD CHECK */

form.addEventListener("submit",function(e){

if(candidatForm.style.display==="block"){

const pw=document.getElementById("candidatPassword").value;
const cpw=document.getElementById("candidatConfirm").value;

if(pw!==cpw){

e.preventDefault();
document.getElementById("errorCand").style.display="block";

}

}


if(orgaForm.style.display==="block"){

const pw=document.getElementById("orgaPassword").value;
const cpw=document.getElementById("orgaConfirm").value;

if(pw!==cpw){

e.preventDefault();
document.getElementById("errorOrga").style.display="block";

}

}

});


/* INIT */

toggleInputs(candidatForm,false);
toggleInputs(orgaForm,false);

</script>



<style>

.pi-role-select{
display:flex;
gap:0.75rem;
}

.pi-role-option{
flex:1;
cursor:pointer;
}

.pi-role-option input{
display:none;
}

.pi-role-card{

display:flex;
flex-direction:column;
align-items:center;

gap:0.3rem;

padding:1rem;

border:2px solid var(--border);

border-radius:var(--radius);

background:var(--bg-input);

text-align:center;

transition:0.2s;

height:150px;

}

.pi-role-card .pi-role-icon{
font-size:1.5rem;
}

.pi-role-card strong{
font-size:0.85rem;
}

.pi-role-desc{
font-size:0.7rem;
}

.pi-role-option input:checked + .pi-role-card{

border-color:var(--accent);
background:var(--accent-dim);

}

.pi-role-option input:checked + .pi-role-card strong{

color:var(--accent);

}

</style>

</body>
</html>