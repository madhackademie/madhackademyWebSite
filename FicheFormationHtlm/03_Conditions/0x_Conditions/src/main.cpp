#include "raylib.h"
#include <cstdio>

int main(void)
{
    // create a windows for the app with size and title as param
    InitWindow(800, 450, "raylib [core] example - basic window");
    while (!WindowShouldClose())
    {
        BeginDrawing();
        // need to clear the background every frame see the tutorial on the hello world section
        ClearBackground(RAYWHITE);
        EndDrawing();
    }
    CloseWindow();

    return 0;
}