#include "raylib.h"
#include <string>
#include <cstdio>
#include <iostream>

/*
1.Dans un premier temps nous devons inclure la lib <string> sans quoi il est impossible de créer des 
variables de type texte moderne.
2.Nous allons ensuite inclure la vieille lib cstdio qu'ont délaissera par la suite, mais il est intéresant de 
pouvoir lire et comprendre ce code qui vient du C et encore très présent voir utilisé.
3. Et en dernier la lib <iostream> qui va de pair avec la lib <string> pour récuperer la saisie clavier
de l'utilisateur et print du text dans la console.
*/

//****CHALLENGE***** 
/*
je fais le choix d'initialiser l'utilisateur ou joueur à l'aide d'une struct dans
la mesure ou ce dernier comprends plusieurs membres ou éléments. Et peut être facilement évoluable 
et maintenable si on doit ajouter des nouveaux éléments (ex : lieu de naissance, sexe, etc...)
*/
//CHALL 
struct userData
{
    int age;
    std::string name;
};

/*
La fonction retourne une struc oui oui je sais pas vue dans le module mais faut rester souple
Si vous pouvez retourner un nombre décimale pourquoi pas une structure ou une autre variable hein ?
En plus, cela vous permet de récuperer plusieurs valeurs avec un archetype tout benef. 
*/
//CHALL 1.Fonction
userData HelloPlayer()
{
    userData temp;//une variable temporaire pour stocker la valeur le temps qu'elle soit traité
    std::cout <<"quel est votre nom : \n";
    std::cin >> temp.name;
    std::cout <<"quel est votre age : \n";
    std::cin >> temp.age;
    return temp;
}

int main(void)
{
    //Premier exercice
    //1.initialiser la variable text
    std::string H_world = "Hello world";
    //2. avec le vieux printf()
    printf("Message i made it special for Yiouuu : %s\n", H_world.c_str());
    //3.with the modern std::cout
    std::cout<<"Mi message : "<<H_world<<"\n";

    //****CHALLENGE APPEL DE LA FONCTION*****
    //initialisation et demande des info clients par l'appel de fonction
    userData player = HelloPlayer();
    //création du message a afficher pour saluer le bon consomateur qui va faire chauffer sa
    //CB pour nous hein hein m'en faut plein !!!
    std::string message = "hello " + player.name + " allonge la thune !";
    
    // create a windows for the app with size and title as param
    InitWindow(800, 450, "GameDevReady_std & fonction");
    while (!WindowShouldClose())
    {
        BeginDrawing();
        // need to clear the background every frame see the tutorial on the hello world section
        ClearBackground(RAYWHITE);
        //E1.4.print with Raylib
        DrawText(H_world.c_str(),250, 200, 50, BLUE);
        //CHALL.2.print
        DrawText(message.c_str(),50,275,40,GOLD);
        EndDrawing();
    }
    CloseWindow();
    return 0;
}