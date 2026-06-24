#include "raylib.h"
#include <cstdio>
#include <string>

struct Fighter
{
    std::string name;
    int hp;
    int attackPower;

    void Attack(Fighter& target)
    {
        target.hp -= attackPower;
        if (target.hp < 0)
            target.hp = 0;
        printf("%s attaque %s pour %d degats !\n", name.c_str(), target.name.c_str(), attackPower);
    }

    bool IsAlive() const
    {
        return hp > 0;
    }

    void PrintStatus() const
    {
        printf("%s — HP: %d | ATK: %d\n", name.c_str(), hp, attackPower);
    }
};

int main(void)
{
    Fighter david{"David", 30, 8};
    Fighter goliath{"Goliath", 50, 12};

    printf("--- Etat initial ---\n");
    david.PrintStatus();
    goliath.PrintStatus();

    david.Attack(goliath);
    goliath.Attack(david);

    printf("--- Apres un echange ---\n");
    david.PrintStatus();
    goliath.PrintStatus();

    InitWindow(800, 450, "GameDevReady — Struct & Methodes");
    while (!WindowShouldClose())
    {
        BeginDrawing();
        ClearBackground(RAYWHITE);
        DrawText(TextFormat("%s  HP: %d", david.name.c_str(), david.hp), 50, 80, 28, BLUE);
        DrawText(TextFormat("%s  HP: %d", goliath.name.c_str(), goliath.hp), 50, 130, 28, MAROON);
        DrawText(TextFormat("David vivant: %s", david.IsAlive() ? "oui" : "non"), 50, 200, 22, DARKGRAY);
        DrawText(TextFormat("Goliath vivant: %s", goliath.IsAlive() ? "oui" : "non"), 50, 230, 22, DARKGRAY);
        EndDrawing();
    }
    CloseWindow();

    return 0;
}
