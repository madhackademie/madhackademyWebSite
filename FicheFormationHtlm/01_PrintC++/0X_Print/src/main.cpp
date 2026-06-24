#include "raylib.h"
#include <cstdio>

int main(void)
{
    // create a windows for the app with size and title as param
    int testPrint = 10;
    float testPrintFloat = 5.5f;
    InitWindow(800, 450, "raylib [core] example - basic window");
    printf("x = %d\n\n",testPrint);
    printf("y = %f\n\n",testPrintFloat);
    while (!WindowShouldClose())
    {
        BeginDrawing();
        // need to clear the background every frame see the tutorial on the hello world section
        ClearBackground(RAYWHITE);
        DrawText(TextFormat("x = %d\ny = %f\n",testPrint, testPrintFloat), 190, 200, 20, LIGHTGRAY);
        EndDrawing();
    }
    CloseWindow();

    return 0;
}