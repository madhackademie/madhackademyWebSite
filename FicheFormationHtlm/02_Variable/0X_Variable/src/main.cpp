#include "raylib.h"

int main(void)
{
    // create a windows for the app with size and title as param
    int age = 45.3;
    int x = true;
    bool test = 12;
    float tailleEnMetre = 1.85f;
    bool estVivant = true;
    char intiale = 'b';

    // pointeur
    int userAge = 10;
    int* intPtr = nullptr;
    intPtr = &userAge;
    *intPtr += 25; // userAge is now 35
    
    InitWindow(800, 450, "raylib [core] example - basic window");

    while (!WindowShouldClose())
    {
        BeginDrawing();
        // need to clear the background every frame see the tutorial on the hello world section
        ClearBackground(RAYWHITE);
        DrawText(TextFormat("userage = %u",userAge), 190, 200, 20, LIGHTGRAY);
        EndDrawing();
    }
    CloseWindow();

    return 0;
}