// pour s'inscrire:
RegistrationController:
class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $hasherPass): Response
    {

        $user = new Users();
        $form = $this->createForm(UserFormType::class, $user);
        $form->handleRequest($request);
        $plaintext = $user->getPassword();

        if ($form->isSubmitted() && $form->isValid()) {
            $hashedPassword = $hasherPass->hashPassword($user, $plaintext);
            $user->setPassword($hashedPassword);

            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('app_home');
        }

        return $this->render('registration/register.html.twig', [
            'user' => $form->createView(),
        ]);
    }
}
register.html.twig:
{# templates/registration/register.html.twig #}

{% extends 'base.html.twig' %}

{% block body %}
	<h1 style="margin-left: 35px;">Formulaire d'Inscription</h1>

 <div style="width: 250px;margin-left:35px">
  {{ form(user) }} 
</div>

{% endblock %}
// pour se connecter:  Pas Encore

Pour ajouter un Produit:

class AjoutProduitController extends AbstractController
{
    #[Route('/produit', name: 'app_ajout_produit')]
    public function ajout_vetement(Request $request, EntityManagerInterface $entityManager, CategoriesRepository $categoriesRepository): Response
    {
        $produit = new Produit();
        $form = $this->createForm(ProduitsType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $uploadedFile = $form->get('image')->getData();
            $newFilename = md5(uniqid()) . '.' . $uploadedFile->guessExtension();

            // Déplacez le fichier vers le répertoire où vous souhaitez le stocker
            $uploadedFile->move(
                $this->getParameter('img_directory'),
                $newFilename
            );

            $couleur = $form->get('couleur')->getData();
            $taille = $form->get('taille')->getData();
            $description = $form->get('description')->getData();
            $descriptionDetaillee = $form->get('descriptionDetaille')->getData();
            $prix = $form->get('prix')->getData();
            $category = $form->get('category')->getData();

            // Enregistrement de nom de fichier dans la table de la BD
            $produit->setImage($newFilename);
            $produit->setTaille($taille);
            $produit->setCouleur($couleur);
            $produit->setDescription($description);
            $produit->setPrix($prix);
            $produit->setDescriptionDetaille($descriptionDetaillee);

            $categoryId = $form->get('category')->getData();
            $category = $categoriesRepository->find($categoryId);
            $produit->setCategory($category);
            
            $entityManager->persist($produit);
            $entityManager->flush();
            return $this->redirectToRoute('app_home');
        }

        return $this->render('ajout_produit/ajoutproduit.html.twig', [
            'controller_name' => 'ProduitController',
            'produit' => $form->createView(),
        ]);
    }
}

{% extends 'base.html.twig' %}
{% block title %}Hello VetementsController!
{%
endblock %}
{% block body %}
	{% include "navbar/navbar.html.twig" %}
	<div class="example-wrapper userForm">
		<h4>Formulaire d'Ajout d'un Produit! ✅</h4>
		{{ form_start(produit) }}
		<div class="mb-1"style="width:250px;">
			<div>
				{{ form_row(produit.image) }}
			</div>
			<div>
				{{ form_row(produit.description) }}
			</div>
			<div>
				{{ form_row(produit.taille) }}
			</div>
			<div>
				{{ form_row(produit.couleur) }}
			</div>
			<div>
				{{ form_row(produit.descriptionDetaille) }}
			</div>
			<div>
				{{ form_row(produit.prix) }}
			</div>
			<div>
				{{ form_row(produit.category) }}
			</div>
			<div>
				{{ form_row(produit.submit) }}
			</div>
			{{ form_end(produit) }}
		</div>
		{% if form_errors(produit) %}
			<div class="alert alert-danger">
				{{ form_errors(produit) }}
			</div>
		{% endif %}
	</div>
{% endblock %}

// pour le panier:
class PanierController extends AbstractController
{
    //pour ajouter des produits au panier
    #[Route('/panier', name: 'afficher_panier')]
    public function afficherPanier(SessionInterface $session): Response
    {
        // Récupérer le panier depuis la session
        $panier = $session->get('panier', []);

        return $this->render('panier/afficher_panier.html.twig', [
            'panier' => $panier,
        ]);
    }
    // pour supprimer un produit du panier
    #[Route('/supprimer-du-panier/{id}', name: 'supprimer_du_panier')]
    public function supprimerDuPanier(int $id, SessionInterface $session): Response
    {
        // Récupérer le panier depuis la session
        $panier = $session->get('panier', []);

        // Rechercher l'index du produit dans le panier
        foreach ($panier as $index => $produit) {
            if ($produit['id'] === $id) {
                // Supprimer le produit du panier
                unset($panier[$index]);
                // Réindexer le tableau après la suppression
                $panier = array_values($panier);
                // Mettre à jour le panier dans la session
                $session->set('panier', $panier);

                // Rediriger vers la page du panier
                return $this->redirectToRoute('afficher_panier');
            }
        }

        // Si le produit n'est pas trouvé, rediriger vers la page du panier
        return $this->redirectToRoute('afficher_panier');
    }

{% extends 'base.html.twig' %}

{% block title %}Panier
{% endblock %}

{% block body %}
	<h1>Panier</h1>
	{% if panier is empty %}
		<p>Votre panier est vide.</p>
	{% else %}
		<ul class="ul_panier">
			{% for item in panier %}
				<li class="liste_panier">
					<img src="{{ asset('img/' ~ item.image) }}" alt="{{ item.description }}" width="200" height="200">
					<h4>{{ item.description }}</h4>

					  <!-- Prix initial -->
                    <h4 class="sousTitres-panier">Prix:<span  id="prix{{item.id}}">{{ item.prix }} €</span></h4>

                     <!-- Ajout du champ de quantité -->
                    <label for="quantity{{ item.id }}"class="sousTitres-panier">Quantité:</label>
                    <input style="width:250px; background-color:pink;" type="number" id="quantity{{ item.id }}" name="quantity{{ item.id }}" min="1" value="1" data-id="{{ item.id }}" class="quantity-input">

                    <!-- Mise à jour du prix en fonction de la quantité -->
                    <h4 class="sousTitres-panier">Total: <span id="total{{ item.id }}">{{ item.prix }} €</span> </h4>


					<a href="{{ path('supprimer_du_panier', {'id': item.id}) }}">Supprimer</a>

				</li>
			{% endfor %}
		</ul>
	{% endif %}

       <script>
        // Script JavaScript pour mettre à jour le total en fonction de la quantité
        document.addEventListener('DOMContentLoaded', function () {
            var quantityInputs = document.querySelectorAll('.quantity-input');

            quantityInputs.forEach(function (input) {
                input.addEventListener('change', function () {
                    var itemId = input.getAttribute('data-id');
                    var itemPrice = parseFloat(document.querySelector('#total' + itemId).innerText);
                    var prixUnitaire = parseFloat(document.querySelector('#prix' + itemId).innerText);
                    var quantity = parseInt(input.value);
                    var total = prixUnitaire * quantity;

                    // Mettre à jour le total affiché
                    document.querySelector('#total' + itemId).innerText = total;
                });
            });
        });
    </script>

	

{% endblock %}

// pour la partie utilisateur:
class UserController extends AbstractController
{
    #[Route('/users', name: 'user_list')]
    public function userList(UsersRepository $userRepository): Response
    {
        // Récupérer la liste des utilisateurs depuis le UserRepository
        $users = $userRepository->findAll();

        // Passer la liste des utilisateurs à la vue
        return $this->render('user/user_list.html.twig', [
            'users' => $users,
        ]);
    }
}

{% extends 'base.html.twig' %}

{% block title %}Liste des Utilisateurs{% endblock %}

{% block body %}
    <h1>Liste des Utilisateurs</h1>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nom d'utilisateur</th>
                <th>Email</th>
                <!-- Ajoutez d'autres colonnes en fonction de votre modèle User -->
            </tr>
        </thead>
        <tbody>
            {% for user in users %}
                <tr>
                    <td>{{ user.id }}</td>
                    <td>{{ user.prenom }}</td>
                    <td>{{ user.email }}</td>
                    <!-- Ajoutez d'autres colonnes en fonction de votre modèle User -->
                </tr>
            {% endfor %}
        </tbody>
    </table>
{% endblock %}
 