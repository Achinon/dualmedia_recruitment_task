# dualmedia_recruitment_task

Rozwiązanie proszę przesłać jako link do publicznego github lub spakowany katalog zip. Ostatni punkt z gwiazdką jest nieobowiązkowy, ale mile widziany.
Postaw projekt na frameworku Symfony 6.4, który będzie spełniać następujące założenia:

1. Baza relacyjna mysql / mariadb.
2. Stwórz migrację startową jako jedną klasę (tylko struktura, bez danych).
3. Stwórz encje zakładające prostą strukturę: produkt, zamówienie i encję dotyczące przedmiotów kupionych w pojedynczym zamówieniu (jedno zamówienie może mieć zakupione więcej niż jeden produkt). Pola w poszczególnych encjach zaproponuj sam, może być kilka najważniejszych według Ciebie.
4. Stwórz kontroler obsługujący zamówienia z metodami: 
	a. Tworzenie zamówienia podając listę identyfikatorów produktów wraz z kupowanymi ilościami. Udane stworzenie zamówienia powinno zwrócić kod http 200 oraz format JSON zamówienia spójny z punktem 4b.
	b. Zwracanie najważniejszych informacji (nie wszystkie) o zamówieniu w formacie JSON po podaniu identyfikatora zamówienia na wejściu.
5. Skonfiguruj Symfony tak, aby w każdym response aplikacji zwracany był header "x-task: 1".
6. *W celu kalkulacji cen i podatków zamówienia stwórz serwis i klasy potrzebne jako collector pattern. 
	a. Załóż że każdy z produktów ma na stałe 23% vat, przelicz zamówienie tak aby posiadało sumę przedmiotów, sumę vat, sumę łączoną. 
	b. Oblicz sumę przedmiotów i sumę vat używając powyższego serwisu jak i powiązanych (z kolekcji, np. jeden oblicza sumę ceny zwykłej, drugi vat, trzeci łączoną, etc) i skonfiguruj je używając Symfony.
