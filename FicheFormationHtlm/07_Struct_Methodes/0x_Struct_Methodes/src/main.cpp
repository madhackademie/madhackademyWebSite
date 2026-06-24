#include "raylib.h"
#include <cstdio>
#include <string>

/*
Exercice 1 — Créez la struct Fighter (name, hp, attackPower)
et deux instances : David et Goliath.
*/

// E1. struct Fighter { ... };


/*
Exercice 2 — Ajoutez les méthodes membres :
  void Attack(Fighter& target);
  bool IsAlive();
  void PrintStatus();
*/


int main(void)
{
    // E1. instanciez david et goliath, puis affichez leurs stats (PrintStatus ou printf)

    // E2. échangez une attaque dans chaque sens

    InitWindow(800, 450, "GameDevReady — Struct & Methodes");
    while (!WindowShouldClose())
    {
        BeginDrawing();
        ClearBackground(RAYWHITE);
        DrawText("Struct & Methodes — voir Struct&Methodes.txt", 80, 200, 20, DARKGRAY);
        // Challenge : afficher les HP restants ici avec TextFormat
        EndDrawing();
    }
    CloseWindow();

    return 0;
}
