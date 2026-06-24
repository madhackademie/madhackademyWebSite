#include "raylib.h"

int main(void)
{
    // create a windows for the app with size and title as param
    int age = 45.3;
    int x = true;
    bool test = 12;
    flaot tailleEnMetre = 1.85f;
    bool estVivant = true;
    char intiale = 'b';
    
    InitWindow(800, 450, "raylib [core] example - basic window");

    while (!WindowShouldClose())
    {
        BeginDrawing();
        // need to clear the background every frame see the tutorial on the hello world section
        ClearBackground(RAYWHITE);
        DrawText("will see how to print data there on next step", 190, 200, 20, LIGHTGRAY);
        EndDrawing();
    }
    CloseWindow();

    return 0;
}