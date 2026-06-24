#include "raylib.h"

int main(void)
{
    // create a windows for the app with size and title as param
    InitWindow(800, 450, "GameDevReady_Container");
    while (!WindowShouldClose())
    {
        BeginDrawing();
        // need to clear the background every frame see the tutorial on the hello world section
        ClearBackground(RAYWHITE);
        DrawText("Congrats! You created your first window!", 190, 200, 20, LIGHTGRAY);
        EndDrawing();
    }
    CloseWindow();

    return 0;
}